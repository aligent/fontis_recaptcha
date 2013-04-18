<?php
/**
 * Created by JetBrains PhpStorm.
 * User: swapna
 * Date: 18/04/13
 * Time: 12:00 PM
 * To change this template use File | Settings | File Templates.
 */
class Fontis_Recaptcha_Model_Observer
{
   /**
     * Validates Customer Account Registration Recaptcha Data
     */
   public function validateRecaptchaCustomerAccountCreation(){
        $bValidateSuccessful=$this->validateRecaptchaData();
        if(!$bValidateSuccessful){
            $data =Mage::app()->getRequest()->getPost();

            Mage::getSingleton('customer/session')->addError('Your reCAPTCHA entry is incorrect. Please try again.');
            Mage::getSingleton('customer/session')->setCustomerFormData($data);

            $this->setNoDisptachFlag();
            $this->redirectReferer();
            return;

        }
    }

    /**
     * Validates Contact Us Recaptcha Data
     */

    public function validateRecaptchaContactsPost(){
        $bValidateSuccessful=$this->validateRecaptchaData();
        if(!$bValidateSuccessful){
            Mage::getSingleton('customer/session')->addError(Mage::helper('contacts')->__('Your reCAPTCHA entry is incorrect. Please try again.'));

            $_SESSION['contact_comment'] = $_POST['comment'];
            $_SESSION['contact_name'] = $_POST['name'];
            $_SESSION['contact_email'] = $_POST['email'];
            $_SESSION['contact_telephone'] = $_POST['telephone'];

            $this->setNoDisptachFlag();
            $this->redirectReferer();
            return;

        }
    }


    /**
     * Validates Send Friend Recaptcha Data
     */
    public function validateRecaptchaSendfriend(){
        $bValidateSuccessful=$this->validateRecaptchaData();
        if(!$bValidateSuccessful){
            $data = Mage::app()->getRequest()->getPost();

            Mage::getSingleton('core/session')->addError('Your reCAPTCHA entry is incorrect. Please try again.');
            Mage::getSingleton('catalog/session')->setSendfriendFormData($data);

            $this->setNoDisptachFlag();
            $this->redirectReferer();
            return;
        }

    }
    /*
     * Validates Review Product Recaptcha Data
     */
    public function validateRecaptchaDataReviewProduct(){
        $bValidateSuccessful=$this->validateRecaptchaData();
        if(!$bValidateSuccessful){
            $data =Mage::app()->getRequest()->getPost();

            Mage::getSingleton('catalog/session')->addError('Your reCAPTCHA entry is incorrect. Please try again.');
            Mage::getSingleton('review/session')->setFormData($data);

            $this->setNoDisptachFlag();
            $this->redirectReferer();
            return;

            }
    }

    /**
     * Validates Recaptcha Data
     * @return Boolean
     */
    public function validateRecaptchaData(){
        if (Mage::getStoreConfig("fontis_recaptcha/recaptcha/customer"))
        { // check that recaptcha is actually enabled

            $privatekey = Mage::getStoreConfig("fontis_recaptcha/setup/private_key");
            // check response
            $resp = Mage::helper("fontis_recaptcha")->recaptcha_check_answer(  $privatekey,
                $_SERVER["REMOTE_ADDR"],
                $_POST["recaptcha_challenge_field"],
                $_POST["recaptcha_response_field"]
            );

        }
        return $resp;
    }

    /**
     * Sets the FLAG_NO_DISPATCH flag to prevent dispatch from happening
     */
    public function setNoDisptachFlag(){
        $request = Mage::app()->getRequest();
        $action = $request->getActionName();
        Mage::app()->getFrontController()->getAction()->setFlag($action, Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
    }

    /*
     * Gets the reference url and sets the header on the response object
     */
    protected function redirectReferer($defaultUrl=null)
    {

        $vRefererUrl = Mage::app()->getRequest()->getServer('HTTP_REFERER');
        if (empty($vRefererUrl)) {
            $vRefererUrl = empty($defaultUrl) ? Mage::getBaseUrl() : $defaultUrl;
        }

        Mage::app()->getResponse()->setRedirect($vRefererUrl);
        return $this;
    }

}