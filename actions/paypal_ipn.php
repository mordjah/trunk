<?php
/*-------------------------------------------------------------------------------
 * Xataface Web Application Framework
 * Copyright (C) 2005-2008 Web Lite Solutions Corp (shannah@sfu.ca)
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *-------------------------------------------------------------------------------
 */
 
/**
 * An action to handle paypals instant payment notifications.
 * 
 * @author Steve Hannah <steve@weblite.ca>
 * @created December 2007
 */
class actions_paypal_ipn {
	function handle(&$params){
		require_once DATAFACE_PATH.'/modules/ShoppingCart/lib/paypal.class.php';
		
		$p = new paypal_class;
		if ( $p->validate_ipn() ){
			if ( !isset($p->ipn_data['invoice']) ){
				error_log('Failed to validate invoice for payment because paypal did not specify an invoice in its ipn data');
				exit;
			}
			
			$invoiceID = $p->ipn_data['invoice'];
			$invoice = df_get_record('dataface__invoices', array('invoiceID'=>$invoiceID));
			if ( !$invoice ){
				error_log('Failed to validate invoice for payment of invoice id '.$invoiceID.' because the invoice does not exist.');
				exit;
			}
			
			$invoice->setValue('status', 'PAID');
			$invoice->setValue('dateModified', date('Y-m-d H:i:s'));
			$invoice->save();
			
		}
	}

}