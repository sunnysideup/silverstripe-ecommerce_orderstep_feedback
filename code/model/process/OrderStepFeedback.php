<?php

/**
 *
 * 1 July bough
 * +10 days start sending
 * +20 days stop sending
 * SO
 * on 11 July
 * 1 July + 10 < Now
 * 1 July + 20 > Now
 *
 */


/**
 *
 * 1 July bought
 * +10 days start sending
 * +20 days stop sending
 * SO
 * on 11 July
 * 1 July + 10 < Now
 * 1 July + 20 > Now
 *
 */


class OrderStepFeedback extends OrderStep
{
    private static $verbose = false;

    /**
     * @var String
     */
    protected $emailClassName = "OrderStepFeedback_Email";

    private static $db = array(
        'SendFeedbackEmail' => 'Boolean',
        'MinDays' => 'Int',
        'MaxDays' => 'Int',
        'MessageAfterProductsList' => 'HTMLText',
        'LinkText' => 'Varchar'
    );

    private static $defaults = array(
        'CustomerCanEdit' => 0,
        'CustomerCanCancel' => 0,
        'CustomerCanPay' => 0,
        'Name' => 'Get feedback',
        'Code' => 'FEEDBACK',
        "ShowAsInProcessOrder" => true,
        "HideStepFromCustomer" => true,
        'SendFeedbackEmail' => true,
        'MinDays' => 10,
        'MaxDays' => 20
    );


    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->addFieldsToTab(
            'Root.CustomerMessage',
            array(
                CheckboxField::create('SendFeedbackEmail', 'Send feedback email to customer?'),
                $minDaysField = NumericField::create('MinDays', "<strong>Min Days</strong> before sending"),
                $maxDaysField = NumericField::create('MaxDays', "<strong>Max Days</strong> before sending")
            ),
            "EmailSubject"
        );
        $minDaysField->setRightTitle('What is the <strong>mininum number of days to wait after completing an order</strong> before this email should be sent?');
        $maxDaysField->setRightTitle('
            What is the <strong>maxinum number of days to wait after completing an order</strong> before this email should be sent?<br>
            <strong>If set to zero, this step will be ignored.</strong>'
        );
        $fields->addFieldsToTab(
            'Root.CustomerMessage',
            array(
                HTMLEditorField::create(
                    'MessageAfterProductsList',
                    _t('OrderStepFeedback.MESSAGEAFTERPRODUCTSLIST', 'Message After Products List')
                )->setRightTitle(
                    'Optional message displayed after the list of products'
                )->setRows(3),
                TextField::create(
                    'LinkText',
                    _t('OrderStepFeedback.BUTTONTEXT', 'Link Text')
                )->setRightTitle('This is the text displayed on the "order again" link/button')
            )
        );
        return $fields;
    }

    public function initStep(Order $order)
    {
        if ($this->SendFeedbackEmail) {
            Config::inst()->update("OrderStep", "number_of_days_to_send_update_email", $this->MaxDays);
        }
        return true;
    }

    public function doStep(Order $order)
    {
        //ignore altogether?
        if ($this->SendFeedbackEmail) {
            // too late to send
            if ($this->isExpiredFeedbackStep($order)) {
                if ($this->Config()->get("verbose")) {
                    DB::alteration_message(" - Time to send feedback is expired");
                }
                return true;
            }
            //is now the right time to send?
            elseif ($this->isReadyToGo($order)) {
                $subject = $this->EmailSubject;
                $message = $this->CustomerMessage;
                if ($this->hasBeenSent($order, false)) {
                    if ($this->Config()->get("verbose")) {
                        DB::alteration_message(" - already sent!");
                    }
                    return true; //do nothing
                } else {
                    if ($this->Config()->get("verbose")) {
                        DB::alteration_message(" - Sending it now!");
                    }
                    return $order->sendEmail(
                        $subject,
                        $message,
                        $resend = false,
                        $adminOnly = false,
                        $this->getEmailClassName()
                    );
                }
            }
            //wait until later....
            else {
                if ($this->Config()->get("verbose")) {
                    DB::alteration_message(" - We need to wait until minimum number of days.");
                }
                return false;
            }
        } else {
            return true;
        }
    }

    /**
     * can continue if emails has been sent or if there is no need to send a receipt.
     * @param DataObject $order Order
     * @return DataObject | Null - DataObject = next OrderStep
     **/
    public function nextStep(Order $order)
    {
        if (
            ! $this->SendFeedbackEmail ||
             $this->hasBeenSent($order, false) ||
             $this->isExpiredFeedbackStep($order)
        ) {
            if ($this->Config()->get("verbose")) {
                DB::alteration_message(" - Moving to next step");
            }
            return parent::nextStep($order);
        }
        if ($this->Config()->get("verbose")) {
            DB::alteration_message(" - no next step: has not been sent");
        }
        return null;
    }

    /**
     * For some ordersteps this returns true...
     * @return Boolean
     **/
    protected function hasCustomerMessage()
    {
        return true;
    }

    /**
     * Explains the current order step.
     * @return String
     */
    protected function myDescription()
    {
        return "The customer is sent a feedback request email.";
    }

    /**
     * returns true if the Minimum number of days is met....
     * @param Order
     * @return Boolean
     */
    protected function isReadyToGo(Order $order)
    {
        if ($this->MinDays) {
            $log = $order->SubmissionLog();
            if ($log) {
                $createdTS = strtotime($log->Created);
                $nowTS = strtotime('now');
                $startSendingTS = strtotime("+{$this->MinDays} days", $createdTS);
                //current TS = 10
                //order TS = 8
                //add 4 days: 12
                //thus if 12 <= now then go for it (start point in time has passed)
                if ($this->Config()->get("verbose")) {
                    DB::alteration_message("Time comparison: Start Sending TS: ".$startSendingTS." current TS: ".$nowTS.". If SSTS > NowTS then Go for it.");
                }
                return ($startSendingTS <= $nowTS) ? true : false;
            } else {
                user_error("can not find order log for ".$order->ID);
                return false;
            }
        } else {
            //send immediately
            return true;
        }
    }

    /**
     * returns true if it is too late to send the feedback step
     * @param Order
     * @return Boolean
     */
    protected function isExpiredFeedbackStep(Order $order)
    {
        if ($this->MaxDays) {
            $log = $order->SubmissionLog();
            if ($log) {
                $createdTS = strtotime($log->Created);
                $nowTS = strtotime('now');
                $stopSendingTS = strtotime("+{$this->MaxDays} days", $createdTS);
                return ($stopSendingTS < $nowTS) ? true : false;
            } else {
                user_error("can not find order log for ".$order->ID);
                return false;
            }
        } else {
            return true;
        }
    }

    public function hasBeenSent(Order $order, $checkDateOfOrder = true)
    {
        return OrderEmailRecord::get()->filter(
            array(
                "OrderEmailRecord.OrderID" => $order->ID,
                "OrderEmailRecord.OrderStepID" => $this->ID,
                "OrderEmailRecord.Result" => 1
            )
        )->count() ? true : false;
    }
}
