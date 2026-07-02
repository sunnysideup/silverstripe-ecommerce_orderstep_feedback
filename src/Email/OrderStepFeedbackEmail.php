<?php

declare(strict_types=1);

namespace Sunnysideup\EcommerceOrderstepFeedback\Email;

use Sunnysideup\Ecommerce\Email\OrderEmail;

class OrderStepFeedbackEmail extends OrderEmail
{
    protected $ss_template = OrderStepFeedbackEmail::class;
}
