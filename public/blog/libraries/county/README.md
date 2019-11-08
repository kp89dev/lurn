# Description
One more jQuery plugin to display countdown. Provides 4 themes and ability to configure the countdown output. Based on
http://egrappler.com/free-jquery-count-down-plugin-county/

# Customization
* endDateTime: The date and time where the count down will stop.
* animation: Animation from count down element, ‘fade’ or ‘scroll’.
* speed: Animation speed for count down element, 500 milliseconds default.
* theme: Color scheme for count down, ‘black’, ‘gray’, ‘red’ and ‘blue’.
* reflection: Whether to show reflection, true or false.
* reflectionOpacity: Opacity for reflection, 0.2 default.

# How to use
Add the following code to to your HTML document.

    <link rel="stylesheet" type="text/css" href="css/county.css" />
    <script src="js/jquery.min.js" type="text/javascript"></script>
    <script src="js/county.js" type="text/javascript"></script>

Add a block element in body of HTML document.

    <div id="count-down"></div>

Call the initializer function with required parameters and your count down is ready.

    $('#count-down').county({ endDateTime: new Date('2016/12/27 10:00:00'), reflection: true, animation: 'scroll', theme: 'black' });
