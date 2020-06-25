<?php

namespace Sunnysideup\EcommerceOrderstepFeedback\Email;

use Sunnysideup\Ecommerce\Email\OrderEmail;

class OrderStepFeedback_Email extends OrderEmail
{
    protected $ss_template = OrderStepFeedback_Email::class;
}
