<?php

namespace Sunnysideup\EcommerceOrderstepFeedback\Email;

use Sunnysideup\Ecommerce\Email\OrderEmail;

class OrderStepFeedbackEmail extends OrderEmail
{
    protected $ss_template = OrderStepFeedbackEmail::class;
}
