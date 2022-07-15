<!--
    Winhweel.js pins and sound example by Douglas McKechie @ www.dougtesting.net
    See website for tutorials and other documentation.

    The MIT License (MIT)

    Copyright (c) 2018 Douglas McKechie

    Permission is hereby granted, free of charge, to any person obtaining a copy
    of this software and associated documentation files (the "Software"), to deal
    in the Software without restriction, including without limitation the rights
    to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
    copies of the Software, and to permit persons to whom the Software is
    furnished to do so, subject to the following conditions:

    The above copyright notice and this permission notice shall be included in all
    copies or substantial portions of the Software.

    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
    IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
    FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
    AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
    LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
    OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
    SOFTWARE.

    ============================================================================
    Note:
        Tick Sound was recorded by DeepFrozenApps and was downloaded from http://soundbible.com/2044-Tick.html
        It has an attribution 3.0 licence.
-->
@include('header')
<link rel="stylesheet" href="{{asset('css/wheel/main.css')}}" type="text/css" />
        <div align="center">
            <h1>Winwheel.js example wheel - pins and sound wheel</h1>
            <p>
                Here is an example of a wheel that contains pins around the outside (these represent the metal rods real prizewheels normally have).
                <br />Also this wheel plays a tick sound when one of the pins passes the pointer.
            </p>
            <br />
            <p>Choose a power setting then press the Spin button. Tick sound will play when a pin passes the pointer.</p>
            <br />
            <table cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td>
                        <div class="power_controls">
                            <br />
                            <img id="spin_button" src="{{url('images/wheel/spin_off.png')}}" alt="Spin" onClick="calculatePrizeOnServer();" />
                            <br /><br />
                            &nbsp;&nbsp;<a href="#" onClick="resetWheel(); return false;">Play Again</a><br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(reset)
                        </div>
                    </td>
                    <td width="438" height="582" class="the_wheel" align="center" valign="center">
                        <canvas id="canvas" width="434" height="434">
                            <p style="{color: white}" align="center">Sorry, your browser doesn't support canvas. Please try another.</p>
                        </canvas>
                    </td>
                </tr>
            </table>
        </div>
@include('footer')
<script type="text/javascript" src="{{asset('js/wheel/Winwheel.js')}}"></script>
<script src="http://cdnjs.cloudflare.com/ajax/libs/gsap/latest/TweenMax.min.js"></script>
        <script>
            // Create new wheel object specifying the parameters at creation time.
            var theWheel = new Winwheel({
                'numSegments'  : 8,     // Specify number of segments.
                'outerRadius'  : 212,   // Set outer radius so wheel fits inside the background.
                'textFontSize' : 28,    // Set font size as desired.
                'segments'     :        // Define segments including colour and text.
                [
                   {'fillStyle' : '#eae56f', 'text' : 'Prize 1'},
                   {'fillStyle' : '#89f26e', 'text' : 'Prize 2'},
                   {'fillStyle' : '#7de6ef', 'text' : 'Prize 3'},
                   {'fillStyle' : '#e7706f', 'text' : 'Prize 4'},
                   {'fillStyle' : '#eae56f', 'text' : 'Prize 5'},
                   {'fillStyle' : '#89f26e', 'text' : 'Prize 6'},
                   {'fillStyle' : '#7de6ef', 'text' : 'Prize 7'},
                   {'fillStyle' : '#e7706f', 'text' : 'Prize 8'}
                ],
                'animation' :           // Specify the animation to use.
                {
                    'type'     : 'spinToStop',
                    'duration' : 5,
                    'spins'    : 8,
                    'callbackFinished' : alertPrize,
                    'callbackSound'    : '',   // Function to call when the tick sound is to be triggered.
                    'soundTrigger'     : 'pin'        // Specify pins are to trigger the sound, the other option is 'segment'.
                },
                'pins' :
                {
                    'number' : 16   // Number of pins. They space evenly around the wheel.
                }
            });

            // -----------------------------------------------------------------
            // This function is called when the segment under the prize pointer changes
            // we can play a small tick sound here like you would expect on real prizewheels.
            // -----------------------------------------------------------------
            var audio = new Audio('{{asset('images/wheel/tick.mp3')}}');  // Create audio object and load tick.mp3 file.

            function playSound()
            {
                // Stop and rewind the sound if it already happens to be playing.
                audio.pause();
                audio.currentTime = 0;

                // Play the sound.
                audio.play();
            }

            // -------------------------------------------------------
            // Called when the spin animation has finished by the callback feature of the wheel because I specified callback in the parameters
            // note the indicated segment is passed in as a parmeter as 99% of the time you will want to know this to inform the user of their prize.
            // -------------------------------------------------------
            function alertPrize(indicatedSegment)
            {
                // Do basic alert of the segment text.
                // You would probably want to do something more interesting with this information.
                alert("You have won " + indicatedSegment.text);
            }

            // =======================================================================================================================
            // Code below for the power controls etc which is entirely optional. You can spin the wheel simply by
            // calling theWheel.startAnimation();
            // =======================================================================================================================
            var wheelPower    = 0;
            var wheelSpinning = false;

            // Light up the spin button by changing it's source image and adding a clickable class to it.
            document.getElementById('spin_button').src = "{{asset('images/wheel/spin_on.png')}}";
            //make spin button clickable
            document.getElementById('spin_button').className = "clickable";

            function calculatePrizeOnServer() {
                var url = "{{ url('calculate_prize') }}";
                console.log('clicked');
                // return false;
                $.ajax({
                    type: "POST",
                    url: url,
                    async: true,
                    data: {'_token': '<?php echo csrf_token(); ?>'},
                    success: function (data) {
                        console.log(wheelSpinning);
                        // Ensure that spinning can't be clicked again while already running.
                        if (wheelSpinning == false) {
                            //number of spins for the wheel
                            theWheel.animation.spins = 100;

                            // Disable the spin button so can't click again while wheel is spinning.
                            document.getElementById('spin_button').src       = "{{asset('images/wheel/spin_off.png')}}";
                            document.getElementById('spin_button').className = "";

                            // Get random angle inside specified segment of the wheel.
                            let stopAt = theWheel.getRandomForSegment(data);
                            console.log(stopAt);

                            // Important thing is to set the stopAngle of the animation before stating the spin.
                            theWheel.animation.stopAngle = stopAt;

                            // Begin the spin animation by calling startAnimation on the wheel object.
                            theWheel.startAnimation();

                            // Set to true so that power can't be changed and spin button re-enabled during
                            // the current animation. The user will have to reset before spinning again.
                            wheelSpinning = true;
                        }
                    }
                })
            }

            // -------------------------------------------------------
            // Function for reset button.
            // -------------------------------------------------------
            function resetWheel()
            {
                theWheel.stopAnimation(false);  // Stop the animation, false as param so does not call callback function.
                theWheel.rotationAngle = 0;     // Re-set the wheel angle to 0 degrees.
                theWheel.draw();                // Call draw to render changes to the wheel.
                // Light up the spin button by changing it's source image and adding a clickable class to it.
                document.getElementById('spin_button').src = "{{asset('images/wheel/spin_on.png')}}";
                //make spin button clickable
                document.getElementById('spin_button').className = "clickable";
                wheelSpinning = false;          // Reset to false to power buttons and spin can be clicked again.
            }
        </script>
