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
				<td align="left">
					<img src="https://caliskan.com.au/wp-content/uploads/2022/07/footer-tempelate-2-e1684209346276.png" width="240">
				</td>
				<td align="right">
					<b>Bill Statement</b>
				</td>
			</tr>
		</table>

		<hr>

		<table border="1" cellpadding="10" cellspacing="0" width="100%">
		<tr>
			<td width="50%" align="left">
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

		<br>

		<h3>My Summary</h3>

		<?php
		$previousAmountDue = $allDebits = $allCredits = 0;
		foreach ($custom_data['history'] as $history) 
		{
			$allDebits += $history['debited'];
			$allCredits += $history['credited'];
		}
		?>
		<table border="0" width="100%">
			<tr>
				<td width="20%">
					<table border="1" width="100%">
						<tr>
							<td>
								Previous Amount <br>Due
							</td>
						</tr>
						<tr>
							<td align="center">
								$<?php echo number_format($previousAmountDue, 2) ?>
							</td>
						</tr>
					</table>
				</td>
				<td valign="bottom" width="5%">+</td>
				<td width="20%">
					<table border="1" width="100%">
						<tr>
							<td>
								Purchases & Other <br>Charges
							</td>
						</tr>
						<tr>
							<td align="center">
								$<?php echo number_format($allDebits, 2) ?>
							</td>
						</tr>
					</table>
				</td>
				<td valign="bottom" width="5%">-</td>
				<td width="20%">
					<table border="1" width="100%">
						<tr>
							<td>
								Payments and <br>Credits
							</td>
						</tr>
						<tr>
							<td align="center">
								$<?php echo number_format($allCredits, 2) ?>
							</td>
						</tr>
					</table>
				</td>
				<td valign="bottom" width="5%">=</td>
				<td width="20%">
					<table border="1" width="100%">
						<tr>
							<td>
								Total Amount <br>Due
							</td>
						</tr>
						<tr>
							<td align="center">
								$<?php echo number_format( ($previousAmountDue + $allDebits - $allCredits), 2 ) ?>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>

		<br>

		<table border="1" cellpadding="10" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th>Date</th>
					<th>Activity</th>
					<th>Credit</th>
					<th>Debit</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($custom_data['history'] as $history) {  ?>
					<tr>
						<td><?php echo $history['date'] ?></td>
						<td><?php echo $history['description'] ?></td>
						<td>$<?php echo number_format($history['credited'], 2) ?></td>
						<td>$<?php echo number_format($history['debited'], 2) ?></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>

		<br>
		<br>
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
