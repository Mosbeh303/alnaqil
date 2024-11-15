<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>كشف حساب الشحنات</title>

		<style>
			.container{
				max-width: 850px;
				margin: auto;
                border-radius: 5px ;
				font-size: 14px;
				line-height: 10px;
				direction: rtl;
				font-family: 'XBRiyaz', sans-serif;
				color: #333;
			}

            .card{
                background-color: #4B5563;
                color: #fff;
                border-radius: 0.25rem;
                padding: 1rem 1.5rem;
                margin-bottom: 20px
            }

            .pageTitle{
                margin-bottom: 25px
            }

            .title{
                font-size: 1.2rem;
                font-weight: 600
            }



		</style>
	</head>

	<body>
		<div class="container">
            <h3 class="pageTitle">تتبع الشحنة رقم {{$number}}</h3>

            @foreach ($tracks as $track)
                <div class="card">
                    <p class="date">{{$track['created_at']}}</p>
                    <p class="title"> {{$action[$track['action']]}} </p>
                    <p class="description"> {{$description[$track['action']]}} {{$track['username']}} </p>
                </div>
            @endforeach

		</div>
	</body>
</html>
