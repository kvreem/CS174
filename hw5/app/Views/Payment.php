<?php
namespace Views;
/**
 * This class will render responses for payments.
 * Either it will be successful or throw exception.
 *
 * @author kareem, kevin, avinash
 */
class Payment extends Base {
    public function renderError($data) {
        $html = $this->render_header(array());
        $html .= '<div class="error">Your credit card is rejected</div>';
        $html .= '<br>Message: ' . $data['message'];
        $html .= '<br><a href="./index.php">Try again!</a>';
        $html .= $this->render_footer(array());
        return $html;
    }
    
    public function renderSuccess() {
        $html = $this->render_header(array());
        $html .= 'Your fresh postcard is sent to the email addresses you provided!';
        $html .= '<br><a href="./index.php">Do it again!</a>';
        $html .= $this->render_footer(array());
        return $html;
    }
}