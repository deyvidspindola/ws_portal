<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1">
    <title></title>
    <style type="text/css">
      table
      {
        font-family:"Trebuchet MS", Arial, Helvetica, sans-serif;
        width:100%;
        border-collapse:collapse;
      }
      table td, th
      {
        border:1px solid #ccc;
        padding:3px 7px 2px 7px;
      }
      table th
      {
        font-size:1em;
        text-align:left;
        padding-top:5px;
        padding-bottom:4px;
        background-color:#CCCCCC;
        color:#000;
      }
      table tr, td
      {
        border:1px solid #ccc;
        padding:3px 7px 2px 7px;
      }
      table tr
      {
        font-size:1em;
        text-align:left;
        padding-top:5px;
        padding-bottom:4px;
        background-color:#CCCCCC;
        color:#000;
      }
    
    </style>
  </head>
  <body>
    <h3>Debito Automatico - Arquivo de Remessa</h3>	
	<p>Data / Hora: <span style="font-weight: bold;">[dtahoras]</span>
	<br/>Periodo de Faturamento: <span style="font-weight: bold;">[diaFaturamento]</span></p>
    <table>
        <tr>
          <th>Banco</th>
          <th>Status</th>
          <th>Obs</th>
        </tr>
        [dadosEmail]       
    </table>
      <p><img src="<?php echo _PROTOCOLO_;?>intranet.sascar.com.br/images/lg_sascar.gif" alt="" width="200" height="46" /></p>
  </body>
<html>