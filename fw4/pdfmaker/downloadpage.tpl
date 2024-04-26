<html>
	<head>
		<style>
			p {
				margin-top:20px;
			}
			.downloadbutton {
				padding: 10px;
				display: inline-block;
				cursor: pointer;
				text-align: center;
				-webkit-user-select: none;
				-moz-user-select: none;
				-ms-user-select: none;
				user-select: none;
				margin-top: 3px;
				margin-bottom: 5px;
				background-color: #1974d2;
				color: white;
				border-radius: 5px;
				box-shadow: 3px 3px 3px #CCC;
				text-decoration: none;
				transition: 0.5s;
			}
			.downloadbutton:hover {
				background-color: #004a97;
			}
		</style>
	</head>
	<body>
		<p>PDFの準備ができました。ダウンロードボタンをクリックして下さい。</p>
<a href="../pdfmaker/pdfmaker_make.php?cmd=download&time={$time}" target="_blank" class="downloadbutton" style="width:300px;">ダウンロード</a>
	</body>
</html>