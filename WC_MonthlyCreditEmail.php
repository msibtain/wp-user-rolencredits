<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

use Dompdf\Dompdf;
use Dompdf\Options;

class WC_MonthlyCreditEmail extends WC_Email {

    private $custom_data = [];

    public function __construct() {

        $this->id          = 'es_monthly_credit_email';
        $this->title       = 'User Monthly Credits Email';
        $this->description = 'User Monthly Credits Email sent to user every month if subscribed.';

        $this->subject = __( '[{site_title}] - Monthly Credit History' ) ;
		$this->heading = __( 'Monthly Credit History' ) ;
        
        $this->template_html  = 'emails/credit-history-email.php' ;
        $this->template_plain = 'emails/plain/credit-history-email.php' ;
        $this->placeholders   = array(
            '{order_date}'   => '',
            '{order_number}' => '',
                ) ;

        parent::__construct();
    }

    public function trigger( $recipient, $custom_data = [] ) {
        if ( ! $recipient ) {
            return;
        }

        $this->recipient = $recipient;
        $this->custom_data = $custom_data;

        $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
    }

    public function get_attachments() {

        ob_start();
        ?>
        <!DOCTYPE html>
        <html>
        <head>
        <style>
            body {
                font-family: Arial, sans-serif;
            }
            .summaryBox {
                font-size: 12px;
                text-align: center;
            }
        </style>
        </head>
        <body>
        <table width="100%" border="0" cellpadding="15">
            <tr>
                <td>

                    <table border="0" width=100%"">
                        <tr>
                            <td align="left" valign="top" width="250">
                                <img src="https://caliskan.com.au/wp-content/uploads/2024/12/caliskan_logo_bnw.jpeg" width="250">
                                <br><br>
                                <b>Caliskan Holding Pty Ltd</b><br>
                                Unit 1 103-107 Batt Street,<br>
                                Jamisontown NSW 2750
                                <br><br>
                                <b>ABN:</b> 40 624 910 032<br>
                                <b>Tel:</b> 1300 388 111<br>
                                <b>Email:</b> sales@caliskan.com.au<br>
                                <b>Website:</b> www.caliskan.com.au
                            </td>
                            <td align="right" valign="top" width="250">
                                <h4 style="padding-top: 0; margin-top: 0;">Account Statement</h4>
                                <?php echo date("d/m/Y"); ?>
                                <br><br>
                                <b>
                                <?php echo $this->custom_data['user_nicename']; ?><br>
                                <?php echo $this->custom_data['user_email'] ?><br>
                                <?php echo $this->custom_data['user_billing_detail']['billing_address_1'] ?> 
                                <?php echo $this->custom_data['user_billing_detail']['billing_address_2'] ?> 
                                <?php echo $this->custom_data['user_billing_detail']['billing_city'] ?> 
                                <?php echo $this->custom_data['user_billing_detail']['billing_state'] ?> <br>
                                <?php echo $this->custom_data['user_billing_detail']['billing_postcode'] ?> 
                                </b>
                            </td>
                        </tr>
                    </table>

                    <br>

                    <table border="1" cellpadding="10" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th width="7%">Date</th>
                                <th>Description</th>
                                <th width="15%">Amount</th>
                                <th width="15%">Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($this->custom_data['history'] as $history) {  ?>
                                <tr>
                                    <td><?php echo date("d/m/Y", $history['raw_date']) ?></td>
                                    <td align="left"><?php echo $history['description'] ?></td>
                                    <td align="right">
                                        $
                                        <?php 
                                        if ( !empty($history['credited']) )
                                        {
                                            echo number_format($history['credited'], 2);
                                        }

                                        if ( !empty($history['debited']) )
                                        {
                                            echo number_format($history['debited'], 2);
                                        }
                                        ?>
                                    </td>
                                    <td align="right">$<?php echo number_format($history['balance'], 2) ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>

                    <br>

                    <?php
                    $previousAmountDue = $allDebits = $allCredits = 0;
                    foreach ($this->custom_data['history'] as $history) 
                    {
                        $allDebits += $history['debited'];
                        $allCredits += $history['credited'];
                    }
                    $previousAmountDue = $allCredits - $allDebits;
                    ?>
                    <table border="0" width="100%">
                        <tr>
                            <td width="16%" class="summaryBox">
                                <table border="1" width="100%" cellspacing="0">
                                    <tr>
                                        <td align="center">
                                            Current<br> &nbsp;
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="center">
                                            0.00
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td width="16%" class="summaryBox">
                                <table border="1" width="100%" cellspacing="0">
                                    <tr>
                                        <td align="center">
                                            1-30 Days Past<br>Invoice date
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="center">
                                            $<?php echo number_format($this->custom_data['last_30_days_amount'], 2); ?>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td width="16%" class="summaryBox">
                                <table border="1" width="100%" cellspacing="0">
                                    <tr>
                                        <td align="center">
                                            31-60 Days Past<br>Invoice date
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="center">
                                            $<?php echo number_format($this->custom_data['last_31_60_days_amount'], 2); ?>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td width="16%" class="summaryBox">
                                <table border="1" width="100%" cellspacing="0">
                                    <tr>
                                        <td align="center">
                                            61-90 Days Past<br>Invoice date
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="center">
                                            $<?php echo number_format($this->custom_data['last_61_90_days_amount'], 2); ?>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td width="16%" class="summaryBox">
                                <table border="1" width="100%" cellspacing="0">
                                    <tr>
                                        <td align="center">
                                            Over 90 Days Past<br>Invoice date
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="center">
                                            $<?php echo number_format($this->custom_data['last_over_90_days_amount'], 2); ?>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td width="16%" class="summaryBox">
                                <table border="1" width="100%" cellspacing="0">
                                    <tr>
                                        <td align="center">
                                            Amount Due<br> &nbsp;
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="center">
                                            $<?php echo number_format($previousAmountDue, 2); ?>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>

                    <br>
                    <br>

                    <hr>
                    <br>

                    <div align="center">
                        <b>Payment Advice<br>
                        Please pay to the following account.<br>
                        Caliskan Holdings Pvt Ltd<br>
                        B2B: 062107<br>
                        Account No. 11200266
                        </b>
                    </div>
                </td>
            </tr>
        </table>
        </body>
        </html>
        <?php
        $html = ob_get_clean();
        
        // Configure Dompdf
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $pdf = new Dompdf($options);
        
        $pdf->loadHtml( $html );
        $pdf->setPaper( 'A4', 'portrait' );
        $pdf->render();

        // Save PDF to a temporary file
        $upload_dir = wp_upload_dir();
        $file_path = $upload_dir['basedir'] . '/invoice-' . time() . '.pdf';
        file_put_contents( $file_path, $pdf->output() );

        return [ $file_path ];
    }

    public function get_content_html() {
        ob_start();
        wc_get_template(
            $this->template_html,
            [
                'email_heading' => $this->get_heading(),
                'custom_data'   => $this->custom_data,
                'email'         => $this,
            ]
        );
        return ob_get_clean();
    }

    public function get_content_plain() {
        ob_start();
        wc_get_template(
            $this->template_plain,
            [
                'email_heading' => $this->get_heading(),
                'custom_data'   => $this->custom_data,
                'email'         => $this,
            ]
        );
        return ob_get_clean();
    }
}

new WC_MonthlyCreditEmail();