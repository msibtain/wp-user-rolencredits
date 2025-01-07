<?php
/**
 * Credit History Email.
 *
 * This template can be overridden by copying it to yourtheme/wp-user-rolencredits/emails/credit-history-email.php.
 */
defined( 'ABSPATH' ) || exit;
?>

<?php
/**
 * Add email header.
 * 
 * @since 1.0
 */
//do_action( 'woocommerce_email_header', $email_heading, $email );

$current_year = date('Y');
$current_month = date('m');

?>

<table width="100%" border="0" cellpadding="15">
<tr>
	<td>

		<table border="0" width=100%"">
			<tr>
				<td align="left" valign="top">
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
				<td align="right" valign="top">
					<h4 style="padding-top: 0; margin-top: 0;">Account Statement</h4>
					<?php echo date("d/m/Y"); ?>
					<br><br>
					<b>
					<?php echo $custom_data['user_nicename']; ?><br>
					<?php echo $custom_data['user_email'] ?><br>
					<?php echo $custom_data['user_billing_detail']['billing_address_1'] ?> 
					<?php echo $custom_data['user_billing_detail']['billing_address_2'] ?> 
					<?php echo $custom_data['user_billing_detail']['billing_city'] ?> 
					<?php echo $custom_data['user_billing_detail']['billing_state'] ?> <br>
					<?php echo $custom_data['user_billing_detail']['billing_postcode'] ?> 
					</b>
				</td>
			</tr>
		</table>

		<?php /* ?>

		<table border="0" cellpadding="10" cellspacing="0" width="100%">
		<tr>
			<td width="10%"></td>
			<td width="90%" align="left">
				<b>
					<?php echo $custom_data['user_nicename']; ?><br>
					<?php echo $custom_data['user_email'] ?><br>
					<?php echo $custom_data['user_billing_detail']['billing_address_1'] ?> 
					<?php echo $custom_data['user_billing_detail']['billing_address_2'] ?> 
					<?php echo $custom_data['user_billing_detail']['billing_city'] ?> 
					<?php echo $custom_data['user_billing_detail']['billing_state'] ?> <br>
					<?php echo $custom_data['user_billing_detail']['billing_postcode'] ?> 
				</br>
			</td>
			<td width="50%">
				<table border="0">
					<tr>
						<td width="50%" align="left">
							Statement Date:
						</td>
						<td width="50%" align="left">
							<?php echo date("M d, Y"); ?>
						</td>
					</tr>
					<tr>
						<td width="50%" align="left">
							Total Amount Due:
						</td>
						<td width="50%" align="left">
							$<?php echo number_format($custom_data['meta']['_total_outstanding_amount'], 2); ?>
						</td>
					</tr>
					<tr>
						<td width="50%" align="left">
							Remember to Pay By:
						</td>
						<td width="50%" align="left">
						<?php echo date("M") ?> <?php echo $custom_data['_due_day'][0] ?>, <?php echo date("Y"); ?>
						</td>
					</tr>
					<tr>
						<td width="50%" align="left">
							Available Credits:
						</td>
						<td width="50%" align="left">
						$<?php echo number_format($custom_data['meta']['_available_credits'], 2); ?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		</table>
		<?php */ ?>
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
				<?php foreach ($custom_data['history'] as $history) {  ?>
					<tr>
						<td><?php echo date("d/m/Y", $history['raw_date']) ?></td>
						<td align="left"><?php echo $history['description'] ?></td>
						<td>
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
						<td>$<?php echo number_format($history['balance'], 2) ?></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>

		<br>

		<?php
		$previousAmountDue = $allDebits = $allCredits = 0;
		foreach ($custom_data['history'] as $history) 
		{
			$allDebits += $history['debited'];
			$allCredits += $history['credited'];
		}
		$previousAmountDue = $allCredits - $allDebits;
		$toalAmountDue = 0;
		?>
		<table border="0" width="100%">
			<tr>
				<td width="16%">
					<table border="1" width="100%" cellspacing="0">
						<tr>
							<td>
								Current<br> &nbsp;
							</td>
						</tr>
						<tr>
							<td align="center">
								$<?php echo number_format($previousAmountDue, 2); ?>
								<?php $toalAmountDue += $previousAmountDue; ?>
							</td>
						</tr>
					</table>
				</td>
				<td width="16%">
					<table border="1" width="100%" cellspacing="0">
						<tr>
							<td>
								1-30 Days
							</td>
						</tr>
						<tr>
							<td align="center">
								$<?php echo number_format($custom_data['last_30_days_amount'], 2); ?>
								<?php $toalAmountDue += $custom_data['last_30_days_amount']; ?>
							</td>
						</tr>
					</table>
				</td>
				<td width="16%">
					<table border="1" width="100%" cellspacing="0">
						<tr>
							<td>
								31-60 Days
							</td>
						</tr>
						<tr>
							<td align="center">
								$<?php echo number_format($custom_data['last_31_60_days_amount'], 2); ?>
								<?php $toalAmountDue += $custom_data['last_31_60_days_amount']; ?>
							</td>
						</tr>
					</table>
				</td>
				<td width="16%">
					<table border="1" width="100%" cellspacing="0">
						<tr>
							<td>
								61-90 Days
							</td>
						</tr>
						<tr>
							<td align="center">
								$<?php echo number_format($custom_data['last_61_90_days_amount'], 2); ?>
								<?php $toalAmountDue += $custom_data['last_61_90_days_amount']; ?>
							</td>
						</tr>
					</table>
				</td>
				<td width="16%">
					<table border="1" width="100%" cellspacing="0">
						<tr>
							<td>
								Over 90 Days
							</td>
						</tr>
						<tr>
							<td align="center">
								$<?php echo number_format($custom_data['last_over_90_days_amount'], 2); ?>
								<?php $toalAmountDue += $custom_data['last_over_90_days_amount']; ?>
							</td>
						</tr>
					</table>
				</td>
				<td width="16%">
					<table border="1" width="100%" cellspacing="0">
						<tr>
							<td>
								Amount Due<br> &nbsp;
							</td>
						</tr>
						<tr>
							<td align="center">
								$<?php echo number_format($toalAmountDue, 2); ?>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>

		<br>
		<br>
<div style="position:absolute; bottom:0">
    <div align="left">
			<b>Goods remain the property of Caliskan Holding PTY LTD until fully paid for.</b>
		</div>

		<hr>
		<br>

		<table border="0" width="100%">
			<tr>
				<td colspan="3" align="left">
					<b>How to Pay</b>
				</td>
			</tr>
			<tr>
				<td width="33%" valign="top" align="left">
					<b>Mail</b><br>
					Please make cheques payable to:<br>
					<b>Caliskan Holding Pty Ltd</b><br>
					Unit 1 103-107 Batt Street,<br>
					Jamisontown NSW 2750 <br>
				</td>
				<td width="33%" valign="top" align="left">
					<b>Credit Card</b>
					<br>
					Credit card merchant fee applies: <br>
					<b>Visa / MasterCard 1%</b>

					<br><br><br>
					<b>Amount Due: $<?php echo number_format($toalAmountDue, 2); ?></b>
				</td>
				<td width="33%" valign="top" align="left">
					<b>Payment Advice<br>
					Please pay to the following account.<br>
					Caliskan Holdings PTY LTD<br>
					B2B: 062107<br>
					Account No. 11200266
					</b>	
				</td>
			</tr>
		</table>
</div>
		<!--<div align="left">-->
		<!--	<b>Goods remain the property of Caliskan Holding Pty Ltd until fully paid for.</b>-->
		<!--</div>-->

		<!--<hr>-->
		<!--<br>-->

		<!--<table border="0" width="100%">-->
		<!--	<tr>-->
		<!--		<td colspan="3" align="left">-->
		<!--			<b>How to Pay</b>-->
		<!--		</td>-->
		<!--	</tr>-->
		<!--	<tr>-->
		<!--		<td width="33%" valign="top" align="left">-->
		<!--			<b>Mail</b><br>-->
		<!--			Please make cheques payable to:<br>-->
		<!--			<b>Caliskan Holding Pty Ltd</b><br>-->
		<!--			Unit 1 103-107 Batt Street,<br>-->
		<!--			Jamisontown NSW 2750 <br>-->
		<!--		</td>-->
		<!--		<td width="33%" valign="top" align="left">-->
		<!--			<b>Credit Card</b>-->
		<!--			<br>-->
		<!--			Credit card merchant fee applies: <br>-->
		<!--			<b>Visa / MasterCard 1%</b>-->

		<!--			<br><br><br>-->
		<!--			<b>Amount Due: $<?php echo number_format($toalAmountDue, 2); ?></b>-->
		<!--		</td>-->
		<!--		<td width="33%" valign="top" align="left">-->
		<!--			<b>Payment Advice<br>-->
		<!--			Please pay to the following account.<br>-->
		<!--			Caliskan Holdings Pvt Ltd<br>-->
		<!--			B2B: 062107<br>-->
		<!--			Account No. 11200266-->
		<!--			</b>	-->
		<!--		</td>-->
		<!--	</tr>-->
		<!--</table>-->
	</td>
</tr>
</table>


<?php
/**
 * Add email footer.
 * 
 * @since 1.0
 */
//do_action( 'woocommerce_email_footer', $email );
?>
