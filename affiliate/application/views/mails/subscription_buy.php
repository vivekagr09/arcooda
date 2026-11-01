
      <tr>
        <td align="center" style="padding:20px 23px 0 23px">
          <table width="600" style="background-color:#FFF; margin:0 auto; border-radius:5px; border-collapse:collapse">
            <tbody>
              <tr>
                <td align="center">
                  <table width="500" style="margin:0 auto">
                    <tbody>
                      <tr>
                        <td align="center" style="padding:15px 0 20px 0; font-family:'Roboto', Arial !important">
                          <?= $mail_templates ?>
                        </td>
                      </tr>
                      <tr>
                        <td align="center" style="padding-bottom:30px">
                          <table style="width:255px; margin:0 auto;">
                            <tbody>
                              <tr>
                                <td width="255" style="background-color:#008AF1; text-align:center; border-radius:5px; vertical-align:middle; padding:13px 0">
                                  <a href="<?= $orderLink ?>" target="_blank" style="outline:0;color:#FFF; text-transform:uppercase; display:block; text-align:center; text-decoration:none; font-weight:bold; font-size:19px;">View Details</a>
                                </td>
                              </tr>
                            </tbody>
                          </table>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </td>
              </tr>
              <tr>
                <td align="center" cellspacing="0" style="padding:0 0 30px 0; vertical-align:middle">
                  <table width="550" style="border-collapse:collapse; background-color:#FaFaFa; margin:0 auto; border:1px solid #E5E5E5">
                    <tbody>
                      <tr>
                        <td width="276" style="vertical-align:top; border-right:1px solid #E5E5E5">
                          <table style="width:100%; border-collapse:collapse">
                            <tbody>
                              <tr>
                                <td style="vertical-align:top; padding:18px 18px 8px 23px; font-family:'Roboto', Arial !important">
                                  <p style="font-size:16px; color:#333333; text-transform:uppercase; font-weight:900; margin:0; font-family:'Roboto', Arial !important">
                                    Summary:
                                  </p>
                                </td>
                              </tr>
                              <tr style="">
                                <td style="vertical-align:top; padding:0 18px 18px 23px">
                                  <table width="100%" style="border-collapse:collapse">
                                    <tbody>
                                      <tr>
                                        <td style="font-family:'Roboto', Arial !important">
                                          <p style="font-size:16px; color:#000; margin:0 0 5px 0; font-family:'Roboto', Arial !important">
                                            Plan
                                          </p>
                                        </td>
                                        <td align="left" style="font-family:'Roboto', Arial !important">
                                          <p style="font-size:16px; color:#000; margin:0 0 5px 0; font-family:'Roboto', Arial !important">
                                            <?= $buy->plan ? $buy->plan->name : '' ?>
                                          </p>
                                        </td>
                                      </tr>
                                      <tr>
                                        <td style="font-family:'Roboto', Arial !important">
                                          <p style="font-size:16px; color:#000; margin:0 0 5px 0; font-family:'Roboto', Arial !important">
                                            Plan Start Date
                                          </p>
                                        </td>
                                        <td align="left" style="font-family:'Roboto', Arial !important">
                                          <p style="font-size:16px; color:#000; margin:0 0 5px 0; font-family:'Roboto', Arial !important">
                                            <?= dateFormat($buy->started_at,'d F Y') ?>
                                          </p>
                                        </td>
                                      </tr>
                                      <tr>
                                        <td style="font-family:'Roboto', Arial !important">
                                          <p style="font-size:16px; color:#000; margin:0 0 5px 0; font-family:'Roboto', Arial !important">
                                            Plan Expire Date
                                          </p>
                                        </td>
                                        <td align="left" style="font-family:'Roboto', Arial !important">
                                          <p style="font-size:16px; color:#000; margin:0 0 5px 0; font-family:'Roboto', Arial !important">
                                            <?= dateFormat($buy->expire_at,'d F Y') ?>
                                          </p>
                                        </td>
                                      </tr>
                                      <tr>
                                        <td style="font-family:'Roboto', Arial !important">
                                          <p style="font-size:16px; color:#000; margin:0 0 5px 0; font-family:'Roboto', Arial !important">
                                            Status
                                          </p>
                                        </td>
                                        <td align="left" style="font-family:'Roboto', Arial !important">
                                          <p style="font-size:16px; color:#000; margin:0 0 5px 0; font-family:'Roboto', Arial !important">
                                            <?= $buy->status_text ?>
                                          </p>
                                        </td>
                                      </tr>
                                      <tr>
                                        <td style="font-family:'Roboto', Arial !important">
                                          <p style="font-size:16px; color:#000; margin:0 0 5px 0; font-family:'Roboto', Arial !important">
                                            Active Status
                                          </p>
                                        </td>
                                        <td align="left" style="font-family:'Roboto', Arial !important">
                                          <p style="font-size:16px; color:#000; margin:0 0 5px 0; font-family:'Roboto', Arial !important">
                                            <?= $buy->active_text ?>
                                          </p>
                                        </td>
                                      </tr>
                                    </tbody>
                                  </table>
                                </td>
                              </tr>
                            </tbody>
                          </table>
                        </td>
                        
                      </tr>
                    </tbody>
                  </table>
                </td>
              </tr>

              
              <tr>
                <td align="center" style="padding-top:24px; padding-bottom:20px">
                  <table width="520" style="border-collapse:collapse">
                    <tbody>
                      <tr>
                        <td align="right" width="425" style="padding-bottom:15px; font-family:'Roboto', Arial !important">
                          <p style="font-size:18px; color:#000; margin:0; font-family:'Roboto', Arial !important">
                            Subtotal:
                          </p>
                        </td>
                        <td align="right" style="padding-bottom:15px; font-family:'Roboto', Arial !important">
                          <p style="font-size:18px; color:#000; margin:0; font-family:'Roboto', Arial !important">
                            <?= $price;?>
                          </p>
                        </td>
                      </tr>
                      
                      <tr>
                        <td align="right" style="padding-bottom:15px; font-family:'Roboto', Arial !important">
                          <p style="font-size:18px; color:#000; font-weight:900; margin:0; font-family:'Roboto', Arial !important">
                            Order Total:
                          </p>
                        </td>
                        <td align="right" style="padding-bottom:15px; font-family:'Roboto', Arial !important">
                          <p style="font-size:18px; color:#bc0101; font-weight:bold; margin:0; font-family:'Roboto', Arial !important">
                            <?= $price;?>
                          </p>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </td>
              </tr>
            </tbody>
          </table>
        </td>
      </tr>
      