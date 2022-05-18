<?php
//$query = $this->db->query("SELECT users.name,users.email,users.invoice_no,users.merchant_order_id,users.mobile,users.address,users.gst,users.add_date,users.merchant_order_id,users.state,package.price,package.package_name FROM `users` INNER JOIN package ON package.id=users.package_id WHERE users.id='" . $this->session->userdata('user_id') . "'")->row();
$query = $this->db->query("SELECT users.name,users.email,users.invoice_no,users.merchant_order_id,users.mobile,users.address,users.gst,users.add_date,users.merchant_order_id,users.state,package.price,package.package_name FROM `users` INNER JOIN package ON package.id=users.package_id WHERE users.id=786")->row();
$temp_total = 0;
$sgst;
$cgst;
$igst;
if ($query->state == 13) {
    $sgst = round($query->price * 9 / 100);
    $cgst = round($query->price * 9 / 100);
    $temp_total = $sgst + $cgst;
} else {
    $igst = round($query->price * 18 / 100);
    $temp_total = $igst;
}
$total = $query->price + $temp_total;
$number = $total;
$no = round($number);
$point = round($number - $no, 2) * 100;
$hundred = null;
$digits_1 = strlen($no);
$i = 0;
$str = array();
$words = array('0' => '', '1' => 'One', '2' => 'Two',
    '3' => 'Three', '4' => 'Four', '5' => 'Five', '6' => 'Six',
    '7' => 'Seven', '8' => 'eight', '9' => 'Nine',
    '10' => 'Ten', '11' => 'Eleven', '12' => 'Twelve',
    '13' => 'Thirteen', '14' => 'Fourteen',
    '15' => 'Fifteen', '16' => 'sixteen', '17' => 'Seventeen',
    '18' => 'Eighteen', '19' => 'Nineteen', '20' => 'Twenty',
    '30' => 'Thirty', '40' => 'Forty', '50' => 'Fifty',
    '60' => 'Sixty', '70' => 'Seventy',
    '80' => 'Eighty', '90' => 'Ninety');
$digits = array('', 'Hundred', 'Thousand', 'Lakh', 'Crore');
while ($i < $digits_1) {
    $divider = ($i == 2) ? 10 : 100;
    $number = floor($no % $divider);
    $no = floor($no / $divider);
    $i += ($divider == 10) ? 1 : 2;
    if ($number) {
        $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
        $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
        $str [] = ($number < 21) ? $words[$number] .
                " " . $digits[$counter] . $plural . " " . $hundred :
                $words[floor($number / 10) * 10]
                . " " . $words[$number % 10] . " "
                . $digits[$counter] . $plural . " " . $hundred;
    } else
        $str[] = null;
}
$str = array_reverse($str);
$netpayable_in_word = implode('', $str);
$points = 0; //($point) ?
?>
<!DOCTYPE html>
<html class="no-js" lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Qpe</title>
        <meta name="description" content="" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="shortcut icon" href="./assets/images/logo.png" type="image/png"/>
        <style>
            body {
                margin: 0;
                padding: 10;
                background-color: #FAFAFA;
                font: 12pt "Tahoma";
            }
            * {
                box-sizing: border-box;
                -moz-box-sizing: border-box;
            }
            .page {
                width: 21cm;
                min-height: 29.7cm;
                padding: 0px;
                margin: 1cm auto;
                border: 1px #D3D3D3 solid;
                border-radius: 5px;
                background: white;
                box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            }
            @page {
                size: A4;
                margin: 0;
            }
            @media print {
                .page {
                    margin: 0;
                    border: initial;
                    border-radius: initial;
                    width: initial;
                    min-height: initial;
                    box-shadow: initial;
                    background: initial;
                    page-break-after: always;
                }
            }
          h4{
            font-size: 15px;
            font-weight: 700;
          }

          h6{
            font-size: 12px;
            font-weight: 800;
          }
          p{
            font-size: 12px;
            font-weight: 400;
            display: contents;
          }
            .invoice-logo img{
              width: 60%;
            }
            .user-detail-text{
              margin-top: 80px;
            }
            .clear {
              clear: both;
            }
            .invoice * h1 {
              color: #4d5357;
              font-weight: lighter;
              font-size: 44px;
              margin: 20px 0 0 0;
            }
            .invoice * .notes {
              float: left;
              width: 50%;
            }
            .invoice * .invoice-totals-wrap {
              float: right;
            }
            .invoice * .terms {
              float: left;
              width: 400px;
              margin: 0 0 40px 0;
              font-size: 12px;
              color: #a1a7ac;
              line-height: 180%;
            }
            .invoice * .terms strong {
              font-size: 16px;
            }

            .invoice * .recipient-address {
              float: left;
            }

            .invoice * .company-address {
              color: #a1a7ac;
              float: right;
              text-align: right;
            }

            .invoice * hr {
              clear: both;
              border: none;
              background: none;
              border-bottom: 1px solid #d6dde2;
            }

            .invoice * table.invoice-items {
              border: 1px #d3d3d3;
              background: #fefefe;
              width: 100%;
              border-collapse: collapse;
            }

            .invoice * table.invoice-totals {
              border: 1px #d3d3d3;
              background: #fefefe;
              border-collapse: collapse;
            }

            .invoice * th {
              font-weight: bold;
            }

            .invoice * th,
            .invoice * td {
              padding: 6px 6px;
              text-align: center;
              border: 1px solid #e0e0e0;
            }

            .invoice * td.first,
            .invoice * th.first {
              text-align: left
            }

            strong,
            b {
              font-weight: bold;
            }

            .signature{
              float: right;
            }

            .signature img{
              width: 30%;
            }
        </style>



    </head>

    <body>
              <table style="background-color: #000; width: 100%;">
                <tr>
                  <td valign="middle" align="left" width="50%">
                    <span style="color: #fff; font-size: 30px;">Invoice</span>
                  </td>
                  <td valign="middle" align="right"  width="50%">
                   <span style="color: #fff; font-weight: 600;padding-right:20px">Invoice No. : QPE-2223-APR-002</span></td>
                  </tr>
              </table>
              <section>
                  <div class="container">
                      <div class="row" style="margin-top: 30px;">
                        <table  width="100%">
                          <tr>
                            <td valign="top" width="100%" colspan="2"> <img src="https://qpe.co.in/assets/images/logo1.png" alt=""></td></tr>
                          <tr>
                            <td valign="top" width="50%">
                              <table>
                                <tbody>
                                  <tr>
                                    <td valign="top" colspan="2"><h4>Appernity Technologies Pvt. Ltd.</h4></td>
                                  </tr>
                                  <tr>
                                    <td scope="col" valign="top" width="30%" style="font-weight: 700;"><span>CIN No. :</span></td>
                                    <td scope="col" valign="top" ><span>U74999HR2017PTC070876</span></td>
                                  </tr>
                                  <tr>
                                    <td scope="col" valign="top" width="30%" style="font-weight: 700;"><span>Address :</span></td>
                                    <td scope="col"><span>4247, IP Extn. - 11, Sector - 49,<br> Faridabad (121001)</span></td>
                                  </tr>
                                  <tr>
                                    <td scope="col" valign="top" width="34%" style="font-weight: 700;"><span>Mobile No. :</span></td>
                                    <td scope="col" valign="top" ><span>+91 - 8287077457</span></td>
                                  </tr>
                                  <tr>
                                    <td scope="col" valign="top" width="30%" style="font-weight: 700;"><span>GSTIN No. :</span></td>
                                    <td scope="col" valign="top" ><span>06AAQCA0627E1ZX</span></td>
                                  </tr>

                                </tbody>
                              </table>
                          </td>
                          <td valign="top" width="50%">
                            <table>
                              <tbody>
                                <tr><td valign="top" colspan="2"><h4><?= $query->name ?></h4></td></tr>
                                <tr>

                                  <td scope="col" valign="top" width="30%" style="font-weight: 700;"><span>Address :</span></td>
                                  <td scope="col" valign="top"  ><span><?= $query->address ?></span></td>
                                </tr>
                                <tr>
                                  <td scope="col" valign="top" width="30%" style="font-weight: 700;"><span>Mobile No. :</span></td>
                                  <td scope="col" valign="top" ><span><p><?= $query->mobile ?></p></span></td>
                                </tr>
                                <tr>
                                  <td scope="col" valign="top" width="30%" style="font-weight: 700;"><span>GSTIN No. :</span></td>
                                  <td scope="col" valign="top"  ><span><p><?= $query->gst ?></p></span></td>
                                </tr>
                                <tr>
                                  <td scope="col" valign="top" width="30%" style="font-weight: 700;"><span>Invoice Date:</span></td>
                                  <td scope="col" valign="top" width="70%" ><span><p><?= date("d-M-Y", strtotime($query->add_date)) ?></p></span></td>
                                </tr>

                              </tbody>
                            </table>
                          </td>
                        </tr>
                        </table>


                      </div>

                                <div class="invoice">
                              <table cellspacing="0" style="width:100%" class="invoice-items">
                                <thead>
                                  <tr>
                                    <th scope="col" class="first" width="50"><span>S. No.</span></th>
                                    <th scope="col" class="first" width="100"><span>Services</span></th>

                                    <th scope="col" width="50"><span>Amount (Rs)</span></th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <tr>
                                    <td class="first">1</td>
                                    <td class="first">QPe <?= $query->package_name ?> Annual Subscription Plan (SAC 997331)</td>
                                    <td><?= $query->price ?></td>

                                  </tr>

                                </tbody>

                              </table>
                              </div>

                                <br> <br>
                                <table cellspacing="0" width="100%" >
                                  <tbody>
                                  <tr>
                                  <td  align="left" valign="bottom" width="60%">

                                <br><strong>In Words:</strong>
                                <?= $netpayable_in_word ?>

                                  </td>
                                  <td valign="top" align="right" width="40%">
                                  <div class="invoice">
                                  <table class="invoice-totals">
                                   <?php
                                    if ($query->state != 13) {
                                        ?>
                                        <tr>
                                            <td scope="col"  style="text-align:right;"><span>IGST(18%)</span></td>
                                            <td scope="col"><span><?= $igst ?></span></td>
                                        </tr>
                                        <?php
                                    }
                                    if ($query->state == 13) {
                                        ?>
                                        <tr>
                                            <td scope="col" width="220" style="text-align:right;"><span>CGST(9%):</span></td>
                                            <td scope="col" width="100"><span><?= @$cgst ?></span></td>
                                        </tr>
                                        <tr>
                                            <td scope="col" width="220" style="text-align:right;"><span>SGST(9%)</span></td>
                                            <td scope="col" width="100"><span><?= @$sgst ?></span></td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                    <tr>
                                        <td scope="col" width="220" style="text-align:right;"><span>Payable Amount</span></td>
                                        <td scope="col" width="100"><span><?= $total ?></span></td>
                                    </tr>
                                  </table>
                                  </div>
                                  </td>
                                  </tr>


                                  </tbody>
                                </table>
                                <br>

                              <br>

                            <table width="100%">
                            <tr>
                            <td valign="top" width="50%">
                                      <h6>Account Holder Name - Appernity Technologies Private Limited</h6>
                                      <h6>AC No. - 87535627543</h6>
                                      <h6>Indusind Bank</h6>
                                      <h6>IFSC Code - NDSB0000702</h6>
                                  </td>
                                <td valign="top" width="50%">

                                      <div class="signature">
                                          <h6><span><p>For</p></span>Appernity Technologies Private Limited</h6>
                                          <img src="https://qpe.co.in/assets/images/signature.png" alt="" height="100" width='150'>
                                          <h6>Authorized Signature</h6>
                                      </div>
                                  </td>
                                  </tr>
                                  <tr><td colspan="2"><hr></td></tr>
                                  <tr><td colspan="2">  <p style="text-align: center; font-size: 10px; display: block;">This is computer generated Invoice.</p></td></tr>
                              </table>


                            </div>




                      </div>

                  </div>
              </section>


    </body>
</html>
