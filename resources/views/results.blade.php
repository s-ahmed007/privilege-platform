@include('header')

<link rel="stylesheet" href="{{ asset('css/results.css') }}">

<div class="container res-container">
    <div class="row banner_top_image_result">
        <div class="banner_top_image_caption mtb-10">
            <h3>Royalty Moments</h1>
            <span>Share your favorite moments at our partner stores to win exciting prizes</span>
        </div>
    </div>
    <div class="timer">
        <ul>
            <li class="time-block"><span id="days"></span>days</li>
            <li class="time-block"><span id="hours"></span>Hours</li>
            <li class="time-block"><span id="minutes"></span>Minutes</li>
            <li class="time-block"><span id="seconds"></span>Seconds</li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-6">
            <h3 style="font-weight: 500">
                COMPETITION TAGLINE: #royaltymoments<br>
                Click Photo > Upload > Get Votes > Win
            </h3>
            <h4>
                Share your favorite moments with us<br>
                Win exciting prizes<br>
                Live like a Royalty
            </h4>
            <h5>
                <b> Grand Prizes:</b> <br>
                1. Iphone X<br>
                2. 2 Nights/ 3 Days accommodation for 2 person in “La Meridian”<br>
                3. Round trip travel tickets for 2 to Cox’s bazar<br>
            </h5>
        </div>
        <div class="col-md-6" style="text-align: center">
            <h3>
                WINNERS
            </h3>
            <div class="row">
                <img src="http://4.bp.blogspot.com/-hg306Y-xvq0/UC0gouaGlaI/AAAAAAAADPs/Ovz1QAP1UuA/s1600/011.gif"
                     style="height: 200px;width: 200px;" class="lazyload" alt="Royalty gif">
            </div>
        </div>
    </div>
</div>
<div class="container-fluid prizes">
    <div class="row">
        <h2>
            GRAND PRIZES
        </h2>
        <div class="col-md-4">
            <img src="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/images/results/prize-1.jpeg" style="height: 308px;width: 308px" class="lazyload" alt="Royalty prize">
        </div>
        <div class="col-md-4">
            <img src="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/images/results/prize-2.jpeg" style="height: 308px;width: 308px" class="lazyload" alt="Royalty prize">
        </div>
        <div class="col-md-4">
            <img src="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/images/results/prize-3.png" style="height: 308px;width: 308px" class="lazyload" alt="Royalty prize">
        </div>
    </div>
</div>
<div class="container">
    <h3>
        SUBMISSION RULE
    </h3>
    <ol class="contest-rule-set">
        <li class="contest-rule">
            How to Enter: You can enter the contest through the Sponsored Royalty Facebook Page. Entrants must fill
            out all required fields on the entry form and participate in the contest. After submitting the required
            information on the entry form, the entrants will receive a confirmation mail.
        </li>
        <li class="contest-rule">
            How to compete: Simply upload your favorite moment while using your Royalty App. Share your experience on
            Facebook. Get the highest amount of like and win exciting prizes.
        </li>
        <li class="contest-rule">
            Eligibility to participate: You can only register once with your Facebook ID and personal E-mail.
        </li>
        <li class="contest-rule">
            Contest duration: state the exact date and time of the contest and when you’ll announce the winners
        </li>
        <li class="contest-rule">
            Conditions of disqualification from contest (violating/not following the rules, compromising content, etc.)
        </li>
        <li class="contest-rule">
            For any further information or query please visit <a href="{{ url('terms&conditions') }}">Terms & Conditions</a>
        </li>
    </ol>

    <h5>
        *DISCLAIMER: These rules are the mere minimum you have to meet. Be careful and check your country laws regarding
        contests. Royalty reserves the authority to transfer User or Partner information to government security
        agents and relevant Government regulator in issue of a trial, a security concern raised by any security agency
        of the government of Bangladesh or for any regulatory compliance that might compel Royalty to release User or
        Partner information.
    </h5>
</div>

<script>
    const second = 1000,
        minute = second * 60,
        hour = minute * 60,
        day = hour * 24;

    let countDown = new Date('Sep 30, 2018 00:00:00').getTime(),
        x = setInterval(function () {

            let now = new Date().getTime(),
                distance = countDown - now;

            document.getElementById('days').innerText = Math.floor(distance / (day)),
                document.getElementById('hours').innerText = Math.floor((distance % (day)) / (hour)),
                document.getElementById('minutes').innerText = Math.floor((distance % (hour)) / (minute)),
                document.getElementById('seconds').innerText = Math.floor((distance % (minute)) / second);

            //do something later when date is reached
            //if (distance < 0) {
            //  clearInterval(x);
            //  'AND THE WINNERS ARE!;
            //}

        }, second)
</script>

@include('footer')