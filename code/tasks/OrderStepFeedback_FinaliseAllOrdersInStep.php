<?php


class OrderStepFeedback_FinaliseAllOrdersInStep extends BuildTask
{
    protected $title = 'Try to finalise all orders in the Send Feedback step';

    protected $description = "Selects all the orders in the send feedback step and tries to finalise them by sending the email.";

    private static $number_of_orders_at_one_time = 5;

    private static $number_of_orders_at_one_time_cli = 500;

    /**
     *@return Integer - number of carts destroyed
     **/
    public function run($request)
    {
        //IMPORTANT!
        $orderStepFeedback = OrderStepFeedback::get()->First();
        if ($orderStepFeedback) {
            Config::inst()->update("OrderStepFeedback", "verbose", true);
            //work out count!

            //set count ...
            $count = null;
            if (isset($_GET["count"])) {
                $count = intval($_GET["count"]);
            }
            if (!intval($count)) {
                $count = Config::inst()->get('OrderStepFeedback_FinaliseAllOrdersInStep', 'number_of_orders_at_one_time');
            }
            if (PHP_SAPI === 'cli') {
                $count = Config::inst()->get('OrderStepFeedback_FinaliseAllOrdersInStep', 'number_of_orders_at_one_time_cli'); 
            }

            //redo ones from the archived step...
            if (isset($_GET["redoall"])) {
                $orderStepArchived = OrderStep_Archived::get()->first();
                if ($orderStepArchived) {
                    $excludeArray = array(0 => 0);
                    $orders = Order::get()
                        ->filter(
                            array(
                                "StatusID" => $orderStepArchived->ID,
                                "OrderEmailRecord.OrderStepID" => $orderStepFeedback->ID,
                                "OrderEmailRecord.Result" => 1
                            )
                        )
                        ->innerJoin("OrderEmailRecord", "\"OrderEmailRecord\".\"OrderID\" = \"Order\".\"ID\"");
                    if ($orders->count()) {
                        foreach ($orders as $order) {
                            $excludeArray[$order->ID] = $order->ID;
                        }
                    }
                    $orders = Order::get()
                        ->filter(array("StatusID" => $orderStepArchived->ID))
                        ->exclude(array("Order.ID" => $excludeArray));
                    if ($orders->count()) {
                        foreach ($orders as $order) {
                            $order->StatusID = $orderStepFeedback->ID;
                            $order->write();
                            DB::alteration_message("Moving Order #".$order->getTitle()." back to Feedback step to try again");
                        }
                    } else {
                        DB::alteration_message("There are no archived orders to redo.", "deleted");
                    }
                } else {
                    DB::alteration_message("Could not find archived order step.", "deleted");
                }
            }

            $position = 0;
            if (isset($_GET["position"])) {
                $position = intval($_GET["position"]);
            }
            if (!intval($position)) {
                $position = intval(Session::get("FinaliseAllOrdersInStep"));
                if (!$position) {
                    $position = 0;
                }
            }
            $orders = Order::get()
                ->filter(array("StatusID" => $orderStepFeedback->ID))
                ->sort(array("ID" => "ASC"))
                ->limit($count, $position);
            if ($orders->count()) {
                DB::alteration_message("<h1>Moving $count Orders (starting from $position)</h1>");
                foreach ($orders as $order) {
                    DB::alteration_message("<h2>Attempting Order #".$order->getTitle()."</h2>");
                    $order->tryToFinaliseOrder();
                    $statusAfterRunningInit = OrderStep::get()->byID($order->StatusID);
                    if ($statusAfterRunningInit) {
                        if ($orderStepFeedback->ID == $statusAfterRunningInit->ID) {
                            DB::alteration_message(" - could not move Order ".$order->getTitle().", remains at <strong>".$orderStepFeedback->Name."</strong>");
                        } else {
                            DB::alteration_message(" - Moving Order #".$order->getTitle()." from <strong>".$orderStepFeedback->Name."</strong> to <strong>".$statusAfterRunningInit->Name."</strong>", "created");
                        }
                    } else {
                        DB::alteration_message(" - Order ".$order->ID." has a non-existing orderstep", "deleted");
                    }
                    $position++;
                    Session::set("FinaliseAllOrdersInStep", $position);
                }
            } else {
                Session::clear("FinaliseAllOrdersInStep");
                DB::alteration_message("<br /><br /><br /><br /><h1>COMPLETED!</h1>All orders have been moved.", "created");
            }
        } else {
            DB::alteration_message("NO feedback order step.", "deleted");
        }
        if (Session::get("FinaliseAllOrdersInStep")) {
            DB::alteration_message("WAIT: we are still moving more orders ... this page will automatically load the next lot in 5 seconds.", "deleted");
        }
    }
}
