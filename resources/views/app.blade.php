<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
	<title>Verify user</title>
	<style type="text/css">
		body{
			font-family: 'Poppins', sans-serif;
		}
		div.box{
			margin: 10px;
			padding: 10px;
		}
		h1{
			padding-bottom: 2px;
			font-size: 17px;
		}
		p{
			padding-bottom: 6px;
			font-size: 15px;
		}
		h1,p{
			color: #2A2929;
			font-weight: 400;
		}
		div.enlace{
			width: 100%;
			display: flex;
			justify-content: center;
		}
		a{
			padding: 7px 18px;
			display: block;
			color: white;
			font-size: 15px;
			font-weight: 400;
			text-decoration: none;
			background-color:#19AF3F;
			transition: all 0.4s ease;
		}
		a:hover{
			background-color:#17A43B;
		}
	</style>
</head>
<body>
	<div class="box">
		<h1>Hola !</h1>
		<p>Verifica el correo electronico para navegar por el sitio web</p>
		<div class="enlace">
			<a href="http://frontend.com/email-confirmation/my-token">Verificar correo</a>
		</div>
		<span>tokenEmail</span>
	</div>
</body>
</html>
