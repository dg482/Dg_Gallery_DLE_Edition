<title>{title}</title>
<!--general stylesheet-->
<style type="text/css">
p { padding: 0; margin: 0; }
h1, h2, h3, p, li { font-family: Georgia, Helvetica, sans-serif, Arial; }
td { vertical-align:top; }
ul, ol { margin: 0; padding: 0; }
.content-item h2 { margin: 0; padding-left:20px; padding-top:3px; padding-bottom:3px; font-size: 24px; color: #626b73 !important; font-weight: normal; font-family:Georgia; }
.content-item p { color: #798692; font-size:14px; line-height:20px; }
.content-item a { color:#353d43; text-decoration: underline; }
.sidebar h4 { margin: 0; padding-bottom:5px; color:#FFFFFF !important; text-transform: uppercase; font-size:10px; letter-spacing:2px; }
.sidebar p { margin:0; padding-top:5px; color: #6e7c88; font-size:12px; line-height:18px; }
.toc a { color:#3d464c; font-style: italic; font-weight:lighter; font-family: Georgia; }
.footer p { font-family: Georgia; margin:0; padding-bottom: 3px; padding-top: 3px; color:#6e7c88; font-size: 12px; }
.footer a { font-family: Georgia; color:#475056; font-size:12px; font-weight:lighter; font-style: italic; text-decoration:none; }
</style>
<!--[if gte mso 9]>
		<style type="text/css">
		.transparent{ background-color: #dfe4e9;}
		</style>
		<![endif]-->
</head>
<body class="body" marginheight="0" topmargin="0" marginwidth="0" leftmargin="0" bgcolor="#cad3db" style="margin: 0px; background-color: #cad3db;">
<table cellspacing="0" border="0" cellpadding="0" width="100%" align="center" style="margin: 0px;">
  <tbody>
    <tr valign="top">
      <td class="ifcase" valign="top"  style=" background-repeat: repeat; background-position: center top;"><!--container-->
        
        <table cellspacing="0" cellpadding="0" border="0" width="700" align="center">
          <tbody>
            <tr>
              <td valign="top" style="padding-bottom:30px; font-family: Georgia, Helvetica, sans-serif, Arial; "><table cellspacing="0" cellpadding="0" border="0" align="center" width="600">
                  <tbody>
                    <tr>
                      <td valign="top">&nbsp;</td>
                    </tr>
                    <tr>
                      <td valign="top" colspan="2"><repeater>
                          <table cellspacing="0" border="0" cellpadding="0" align="center" width="600" style="margin: 0px; padding-bottom:20px;" class="content-item">
                            <tbody>
                              <tr>
                                <td valign="top"><table cellspacing="0" border="0" cellpadding="0" align="center" width="600" style="border:10px; border-style:solid; border-color:#f3f5f7; background-color: #dfe4e9;">
                                    <tbody>
                                      <tr>
                                        <td valign="top" style="padding-top:20px; padding-bottom:20px;"><table cellspacing="0" border="0" cellpadding="0" align="center" width="580" style="">
                                            <tbody>
                                              <tr>
                                                <td valign="top" height="24" style="background-color: #cbd3db;"><h2>
                                                    <singleline label="Title">{title}</singleline>
                                                  </h2></td>
                                              </tr>
                                            </tbody>
                                          </table>
                                          <table cellspacing="0" border="0" cellpadding="0" align="center" width="540" style="padding-top:20px;">
                                            <tbody>
                                              <tr>
                                                <td valign="top"><table cellspacing="0" border="0" cellpadding="0" align="center" width="540">
                                                    <tbody>
                                                      <tr>
                                                        <td valign="top"><multiline label="Description">
                                                            <p>{message}</p>
                                                          </multiline></td>
                                                      </tr>
                                                      <tr>
                                                        <td height="5" style="height:5px; line-height:0.5;">&nbsp;</td>
                                                      </tr>
                                                    </tbody>
                                                  </table></td>
                                              </tr>
                                            </tbody>
                                          </table></td>
                                      </tr>
                                    </tbody>
                                  </table></td>
                              </tr>
                              <tr>
                                <td valign="top" style="padding-top: 20px; padding-bottom: 0px;">&nbsp;</td>
                              </tr>
                            </tbody>
                          </table>
                        </repeater></td>
                    </tr>
                  </tbody>
                </table></td>
            </tr>
          </tbody>
        </table> 
        <!--/container--></td>
    </tr>
  </tbody>
</table>