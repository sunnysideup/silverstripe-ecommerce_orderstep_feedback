2020-06-25 02:18

# running php upgrade upgrade see: https://github.com/silverstripe/silverstripe-upgrader
cd /var/www/upgrades/ecommerce_orderstep_feedback
php /var/www/ss3/upgrader/vendor/silverstripe/upgrader/bin/upgrade-code upgrade /var/www/upgrades/ecommerce_orderstep_feedback/ecommerce_orderstep_feedback  --root-dir=/var/www/upgrades/ecommerce_orderstep_feedback --write -vvv
Writing changes for 4 files
Running upgrades on "/var/www/upgrades/ecommerce_orderstep_feedback/ecommerce_orderstep_feedback"
[2020-06-25 14:18:28] Applying RenameClasses to EcommerceOrderstepFeedbackTest.php...
[2020-06-25 14:18:28] Applying ClassToTraitRule to EcommerceOrderstepFeedbackTest.php...
[2020-06-25 14:18:28] Applying RenameClasses to OrderStepFeedback_Email.php...
[2020-06-25 14:18:28] Applying ClassToTraitRule to OrderStepFeedback_Email.php...
[2020-06-25 14:18:28] Applying RenameClasses to OrderStepFeedback.php...
[2020-06-25 14:18:28] Applying ClassToTraitRule to OrderStepFeedback.php...
[2020-06-25 14:18:28] Applying UpdateConfigClasses to config.yml...
PHP Warning:  Invalid argument supplied for foreach() in /var/www/ss3/upgrader/vendor/silverstripe/upgrader/src/UpgradeRule/YML/YMLUpgradeRule.php on line 32
modified:	tests/EcommerceOrderstepFeedbackTest.php
@@ -1,4 +1,6 @@
 <?php
+
+use SilverStripe\Dev\SapphireTest;

 class EcommerceOrderstepFeedbackTest extends SapphireTest
 {

modified:	src/Email/OrderStepFeedback_Email.php
@@ -2,12 +2,15 @@

 namespace Sunnysideup\EcommerceOrderstepFeedback\Email;

-use OrderEmail;
+
+use Sunnysideup\EcommerceOrderstepFeedback\Email\OrderStepFeedback_Email;
+use Sunnysideup\Ecommerce\Email\OrderEmail;
+


 class OrderStepFeedback_Email extends OrderEmail
 {
-    protected $ss_template = 'OrderStepFeedback_Email';
+    protected $ss_template = OrderStepFeedback_Email::class;
 }



modified:	src/Model/Process/OrderStepFeedback.php
@@ -2,15 +2,26 @@

 namespace Sunnysideup\EcommerceOrderstepFeedback\Model\Process;

-use OrderStep;
-use CheckboxField;
-use NumericField;
-use HTMLEditorField;
-use TextField;
-use Order;
-use Config;
-use DB;
-use OrderEmailRecord;
+
+
+
+
+
+
+
+
+
+use Sunnysideup\EcommerceOrderstepFeedback\Email\OrderStepFeedback_Email;
+use SilverStripe\Forms\CheckboxField;
+use SilverStripe\Forms\NumericField;
+use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
+use SilverStripe\Forms\TextField;
+use Sunnysideup\Ecommerce\Model\Order;
+use SilverStripe\Core\Config\Config;
+use Sunnysideup\Ecommerce\Model\Process\OrderStep;
+use SilverStripe\ORM\DB;
+use Sunnysideup\Ecommerce\Model\Process\OrderEmailRecord;
+


 /**
@@ -46,7 +57,7 @@
     /**
      * @var String
      */
-    protected $emailClassName = "OrderStepFeedback_Email";
+    protected $emailClassName = OrderStepFeedback_Email::class;


 /**
@@ -147,7 +158,7 @@
     public function initStep(Order $order)
     {
         if ($this->SendFeedbackEmail) {
-            Config::modify()->update("OrderStep", "number_of_days_to_send_update_email", $this->MaxDays);
+            Config::modify()->update(OrderStep::class, "number_of_days_to_send_update_email", $this->MaxDays);
         }
         return true;
     }

modified:	_config/config.yml
@@ -5,4 +5,4 @@
   - 'cms/*'
   - 'ecommerce/*'
 ---
-
+{  }

Writing changes for 4 files
✔✔✔