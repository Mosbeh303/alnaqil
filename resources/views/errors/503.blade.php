{{-- @extends('errors::minimal')

@section('title', __('Service Unavailable'))
@section('code', '503')
@section('message', __('Service Unavailable')) --}}


<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <title>الناقل المحلي</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Readex+Pro&display=swap" rel="stylesheet">

  <style>
    * {
      font-family: 'Readex Pro', sans-serif;
      line-height: 1.8;
    }

    body,
    html {
      height: 100%;
      margin: 0;
      direction: rtl;
    }

    .bgimg {
      /* background-image: url('bg.jpg'); */
      height: 100%;
      background-position: center;
      background-size: cover;
      position: relative;
      color: #333;
      font-family: "Courier New", Courier, monospace;
      font-size: 25px;
    }

    .topleft {
      position: absolute;
      top: 0;
      left: 16px;
    }

    .bottomleft {
      position: absolute;
      bottom: 0;
      left: 16px;
    }

    .middle {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      text-align: center;
    }

    @media (max-width: 767px) {
      .middle {
        position: initial;
        transform: initial;
        text-align: center;
        padding: 0 20px;
      }
    }

    hr {
      margin: auto;
      width: 40%;
    }

    ul {
      list-style: none;
      padding: 0;
    }

    li {
      display: inline-block;
      margin-left: 15px;
    }

    /* a{
      color: #fff;
    } */
  </style>
</head>

<body>
  <div class="bgimg">

    <div class="middle">
      <img src="https://alnaqil.sa/images/logo1.png" style="width: 200px;">
      <h1 style="display: none;"> الناقل المحلي</h1>
      <h2>الموقع تحت التحديث حاليا! أعد المحاولة لاحقا.</h1>
        <hr>
        <p>
          الناقل المحلي
          منصة لوجستية لتحسين وإدارة خدمة التوصيل للتجارة الالكترونية
        </p>
        <ul>
          <li><a href="https://api.whatsapp.com/send?phone=966590700745" title="Whatsapp"> <i
                class="fa fa-whatsapp"></i></a></li>
          <li><a href="https://twitter.com/alnaqilsa" title="Twitter"> <i class="fa fa-twitter"></i></a></li>
          <li><a href="https://www.instagram.com/alnaqilsa/" title="Instagram"> <i class="fa fa-instagram"></i></a></li>
          <li><a href="https://www.snapchat.com/add/alnaqilsa" title="Snapchat"> <i class="fa fa-snapchat"></i></a></li>
        </ul>
    </div>

  </div>
</body>

</html>