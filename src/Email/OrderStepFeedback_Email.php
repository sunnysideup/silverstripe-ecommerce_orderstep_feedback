<?php

namespace Sunnysideup\EcommerceOrderstepFeedback\Email;


use Sunnysideup\EcommerceOrderstepFeedback\Email\OrderStepFeedback_Email;
use Sunnysideup\Ecommerce\Email\OrderEmail;



class OrderStepFeedback_Email extends OrderEmail
{
    protected $ss_template = OrderStepFeedback_Email::class;
}

