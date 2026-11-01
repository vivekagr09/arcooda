<head>
<style type="text/css">
    @import url('https://fonts.googleapis.com/css?family=Roboto:400,300,500,700,700italic,900');
    @import url('https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css');
    
    body {
        font-family: 'Roboto', Arial, sans-serif !important;
        background-color: #f4f4f4; /* Added a background color */
        color: #333; /* Added a text color */
        line-height: 1.6;
    }
    
    a[href^="tel"] {
        color: inherit;
        text-decoration: none;
        outline: 0;
        font-weight: bold; /* Make telephone links bold */
    }
    
    a:hover, a:active, a:focus {
        outline: 0;
        text-decoration: underline; /* Added underline on hover */
        color: #007bff; /* Added hover color */
    }
    
    a:visited {
        color: #6c757d; /* Changed the visited link color */
    }
    
    span.MsoHyperlink {
        mso-style-priority: 99;
        color: inherit;
    }
    
    span.MsoHyperlinkFollowed {
        mso-style-priority: 99;
        color: inherit;
    }

    /* New Styles */
    h1, h2, h3, h4, h5, h6 {
        font-weight: 700;
        margin-bottom: 20px;
    }

    p {
        margin-bottom: 20px;
    }

    .btn {
        padding: 10px 20px;
        background-color: #007bff;
        color: #fff;
        border-radius: 4px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
    }

    .btn:hover {
        background-color: #0056b3;
    }
</style>
    <?php if(isset($bootstrap_cdn)) { ?>
       <style type="text/css">
           .table {
                margin-bottom: 10px;
                border: 1px solid #dee2e6;
                width: 100%;
                max-width: 100%;
                margin-bottom: 1rem;
                background-color: transparent;
                border-collapse: collapse;
            }
            .table td, .table th {
                border: 1px solid #dee2e6;
                padding: 14px 12px;
                vertical-align: middle;
            }
       </style>
    <?php } ?>
</head>
    <body style="margin: 0; padding: 0; background-color: linear-gradient(90deg, rgba(136,136,136,1) 0%, rgba(190,190,190,1) 0%, rgba(190,189,189,0) 100%);" >
    <div style="background: transparent">
          <div style="display:none;font-size:1px;color:#333333;line-height:1px;max-height:0px;max-width:0px;opacity:0;overflow:hidden;">
            Questions? Call us any time 24/7 at <?= $mob ?> or simply reply to this email | <?= $mail ?>
          </div>
          <table cellspacing="0" style="margin:0 auto; width:100%; border-collapse:collapse; font-family:'Roboto', Arial !important; border-spacing: 0 !important; background-color: rgba(255,255,255,0.5)">
            <tbody>