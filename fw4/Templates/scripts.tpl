


<!-- jquery -->
<script
  src="https://code.jquery.com/jquery-3.6.4.min.js"
  integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8="
  crossorigin="anonymous"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

<!-- colorpicker -->
<link rel="stylesheet" href="color-picker/asColorPicker.css">
<script src="color-picker/jquery-asColor.js"></script>
<script src="color-picker/jquery-asGradient.js"></script>
<script src="color-picker/jquery-asColorPicker.js"></script>

<!-- Chart -->
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.bundle.js"></script>

<!-- Cookie -->
<script src="js/js.cookie.js"></script>

<!-- dropdown -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- html2canvas -->
<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>

<!-- Vimeo -->
<script src="https://player.vimeo.com/api/player.js"></script>

<!-- Timepicker -->
<script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>

<!-- air-datepicker -->
<script src="https://cdn.jsdelivr.net/npm/air-datepicker@3.3.0/air-datepicker.min.js"></script>

<!-- SQUARE -->
{if $testserver }
<!--	<script type="text/javascript" src="https://js.squareupsandbox.com/v2/paymentform"></script>-->
	<script type="text/javascript" src="https://sandbox.web.squarecdn.com/v1/square.js"></script>
{else}
<!--	<script type="text/javascript" src="https://js.squareup.com/v2/paymentform"></script>-->
	<script type="text/javascript" src="https://web.squarecdn.com/v1/square.js"></script>
{/if}

<!-- google map -->
{if $setting.api_key_map != ""}
	<script>
		status_map=0;
		function initMap(){
			status_map=1;
		}
	</script>
	<script src="https://maps.googleapis.com/maps/api/js?key={$setting.api_key_map}&libraries=geometry,places&callback=initMap&loading=async"></script>
{/if}
