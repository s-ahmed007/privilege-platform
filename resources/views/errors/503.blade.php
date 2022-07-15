<!DOCTYPE html>
<!--[if IE 8 ]>
<html class="no-js oldie ie8" lang="en">
   <![endif]-->
   <!--[if IE 9 ]>
   <html class="no-js oldie ie9" lang="en">
      <![endif]-->
      <!--[if (gte IE 9)|!(IE)]><!-->
      <html class="no-js" lang="en">
         <!--<![endif]-->
         <head>
            <!--- Basic Page Needs
               ================================================== -->
            <meta charset="utf-8">
            <title>STAY TUNED | royaltybd.com</title>
            <meta name="author" content="">
            <!-- Mobile Specific Metas
               ================================================== -->
            <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
            <link rel="icon" href="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/icon/top-logo-user.png">
            <!-- <link href="{{asset('css/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet"> -->
                <link href="{{asset('css/boxicons/css/boxicons.min.css')}}" rel="stylesheet">
                <link href="{{asset('css/venobox/venobox.css')}}" rel="stylesheet">
                <link href="{{asset('css/remixicon/remixicon.css')}}" rel="stylesheet">
                <link href="{{asset('css/stylenew.css')}}" rel="stylesheet">
            <style>
               @import url('https://fonts.googleapis.com/css?family=Montserrat&display=swap');
               html {
               font-family: sans-serif;
               -ms-text-size-adjust: 100%;
               -webkit-text-size-adjust: 100%;
               }
               body {
               margin: 0;
               }
               article,
               aside,
               details,
               figcaption,
               figure,
               footer,
               header,
               hgroup,
               main,
               nav,
               section,
               summary {
               display: block;
               }
               audio,
               canvas,
               progress,
               video {
               display: inline-block;
               vertical-align: baseline;
               }
               audio:not([controls]) {
               display: none;
               height: 0;
               }
               [hidden],
               template {
               display: none;
               }
               a {
               background: transparent;
               }
               a:active,
               a:hover {
               outline: 0;
               }
               abbr[title] {
               border-bottom: 1px dotted;
               }
               b,
               strong {
               font-weight: bold;
               }
               dfn {
               font-style: italic;
               }
               h1 {
               font-size: 2em;
               margin: 0.67em 0;
               }
               mark {
               background: #ff0;
               color: #000;
               }
               small {
               font-size: 80%;
               }
               sub,
               sup {
               font-size: 75%;
               line-height: 0;
               position: relative;
               vertical-align: baseline;
               }
               sup {
               top: -0.5em;
               }
               sub {
               bottom: -0.25em;
               }
               img {
               border: 0;
               }
               svg:not(:root) {
               overflow: hidden;
               }
               figure {
               margin: 1em 40px;
               }
               hr {
               -moz-box-sizing: content-box;
               box-sizing: content-box;
               height: 0;
               }
               pre {
               overflow: auto;
               }
               code,
               kbd,
               pre,
               samp {
               font-family: monospace, monospace;
               font-size: 1em;
               }
               button,
               input,
               optgroup,
               select,
               textarea {
               color: inherit;
               font: inherit;
               margin: 0;
               }
               button {
               overflow: visible;
               }
               button,
               select {
               text-transform: none;
               }
               button,
               html input[type="button"],
               input[type="reset"],
               input[type="submit"] {
               -webkit-appearance: button;
               cursor: pointer;
               }
               button[disabled],
               html input[disabled] {
               cursor: default;
               }
               button::-moz-focus-inner,
               input::-moz-focus-inner {
               border: 0;
               padding: 0;
               }
               input {
               line-height: normal;
               }
               input[type="checkbox"],
               input[type="radio"] {
               box-sizing: border-box;
               padding: 0;
               }
               input[type="number"]::-webkit-inner-spin-button,
               input[type="number"]::-webkit-outer-spin-button {
               height: auto;
               }
               input[type="search"] {
               -webkit-appearance: textfield;
               -moz-box-sizing: content-box;
               -webkit-box-sizing: content-box;
               box-sizing: content-box;
               }
               input[type="search"]::-webkit-search-cancel-button,
               input[type="search"]::-webkit-search-decoration {
               -webkit-appearance: none;
               }
               fieldset {
               border: 1px solid #c0c0c0;
               margin: 0 2px;
               padding: 0.35em 0.625em 0.75em;
               }
               legend {
               border: 0;
               padding: 0;
               }
               textarea {
               overflow: auto;
               }
               optgroup {
               font-weight: bold;
               }
               table {
               border-collapse: collapse;
               border-spacing: 0;
               }
               td,
               th {
               padding: 0;
               }
               /*
               /* Basic Setup Styles - Top Elements
               /* =================================================================== */
               /* NOTE
               html is set to 62.5% so that if you use REMS, all the measurements
               are based on 10px sizing. So basically 1.5rem = 15px  */
               html {
               -webkit-font-smoothing: antialiased;
               -moz-osx-font-smoothing: grayscale;
               font-size: 62.5%;
               -webkit-box-sizing: border-box;
               -moz-box-sizing: border-box;
               box-sizing: border-box;
               }
               html,
               body {
               height: 100%;
               }
               *,
               *:before,
               *:after {
               box-sizing: inherit;
               }
               body {
               font-weight: normal;
               line-height: 1;
               text-rendering: optimizeLegibility;
               }
               /*
               /* Typography
               ====================================================================== */
               div,
               dl,
               dt,
               dd,
               ul,
               ol,
               li,
               h1,
               h2,
               h3,
               h4,
               h5,
               h6,
               pre,
               form,
               p,
               blockquote,
               th,
               td {
               margin: 0;
               padding: 0;
               }
               h1,
               h2,
               h3,
               h4,
               h5,
               h6 {
               text-rendering: optimizeLegibility;
               line-height: 1.2;
               }
               hr {
               border: solid #ddd;
               border-width: 1px 0 0;
               clear: both;
               margin: 11px 0 24px;
               height: 0;
               }
               em,
               i {
               font-style: italic;
               line-height: inherit;
               }
               strong,
               b {
               font-weight: bold;
               line-height: inherit;
               }
               small {
               font-size: 60%;
               line-height: inherit;
               }
               ul {
               list-style: none;
               }
               ol {
               list-style: decimal;
               }
               ol,
               ul.square,
               ul.circle,
               ul.disc {
               margin-left: 17px;
               }
               ul.square {
               list-style: square;
               }
               ul.circle {
               list-style: circle;
               }
               ul.disc {
               list-style: disc;
               }
               ul li {
               padding-left: 4px;
               }
               ul ul,
               ul ol,
               ol ol,
               ol ul {
               margin: 3px 0 3px 17px;
               }
               /* links
               ---------------------------------------------------------------------- */
               a {
               text-decoration: none;
               line-height: inherit;
               }
               a img {
               border: none;
               }
               a:focus {
               outline: none;
               }
               p a,
               p a:visited {
               line-height: inherit;
               }
               /*
               /* Media
               /* =================================================================== */
               img,
               video,
               embed,
               object {
               max-width: 100%;
               height: auto;
               }
               object,
               embed {
               height: 100%;
               }
               img {
               -ms-interpolation-mode: bicubic;
               }
               /*
               /* Grid
               ===================================================================== */
               .row {
               width: 94%;
               max-width: 1024px;
               margin: 0 auto;
               }
               .ie8 .row {
               width: 1024px;
               }
               .narrow .row {
               max-width: 980px;
               }
               .row .row {
               width: auto;
               max-width: none;
               margin-left: -18px;
               margin-right: -18px;
               }
               .row:before,
               .row:after {
               content: "";
               display: table;
               }
               .row:after {
               clear: both;
               }
               .column,
               .columns,
               .bgrid {
               position: relative;
               padding: 0 18px;
               min-height: 1px;
               float: left;
               }
               .column.centered,
               .columns.centered {
               float: none;
               margin: 0 auto;
               }
               .row.collapsed > .column,
               .row.collapsed > .columns,
               .column.collapsed,
               .columns.collapsed {
               padding: 0;
               }
               [class*="column"] + [class*="column"]:last-child {
               float: right;
               }
               [class*="column"] + [class*="column"].end {
               float: right;
               }
               .one,
               .row .one {
               width: 8.33333%;
               }
               .two,
               .row .two {
               width: 16.66667%;
               }
               .three,
               .row .three {
               width: 25%;
               }
               .four,
               .row .four {
               width: 33.33333%;
               }
               .five,
               .row .five {
               width: 41.66667%;
               }
               .six,
               .row .six {
               width: 50%;
               }
               .seven,
               .row .seven {
               width: 58.33333%;
               }
               .eight,
               .row .eight {
               width: 66.66667%;
               }
               .nine,
               .row .nine {
               width: 75%;
               }
               .ten .row .ten {
               width: 83.33333%;
               }
               .eleven,
               .row .eleven {
               width: 91.66667%;
               }
               .twelve,
               .row .twelve {
               width: 100%;
               }
               /* tablets
               --------------------------------------------------------------- */
               @media screen and (max-width:768px) {
               .row {
               width: auto;
               padding-left: 30px;
               padding-right: 30px;
               }
               .row .row {
               padding-left: 0;
               padding-right: 0;
               margin-left: -15px;
               margin-right: -15px;
               }
               .column,
               .columns {
               padding: 0 15px;
               }
               .tab-fourth,
               .row .tab-fourth {
               width: 25%;
               }
               .tab-third,
               .row .tab-third {
               width: 33.33333%;
               }
               .tab-half,
               .row .tab-half {
               width: 50%;
               }
               .tab-2thirds,
               .row .tab-2thirds {
               width: 66.66667%;
               }
               .tab-3fourths,
               .row .tab-3fourths {
               width: 75%;
               }
               .tab-whole,
               .row .tab-whole {
               width: 100%;
               }
               }
               /* mobile
               --------------------------------------------------------------- */
               @media screen and (max-width:600px) {
               .row {
               padding-left: 25px;
               padding-right: 25px;
               }
               .row .row {
               margin-left: -10px;
               margin-right: -10px;
               }
               .column,
               .columns {
               padding: 0 10px;
               }
               .mob-fourth,
               .row .mob-fourth {
               width: 25%;
               }
               .mob-half,
               .row .mob-half {
               width: 50%;
               }
               .mob-3fourths,
               .row .mob-3fourths {
               width: 75%;
               }
               .mob-whole,
               .row .mob-whole {
               width: 100%;
               }
               }
               /* small mobile devices
               --------------------------------------------------------------- */
               @media screen and (max-width:400px) {
               .row {
               padding-left: 30px;
               padding-right: 30px;
               }
               .row .row {
               padding-left: 0;
               padding-right: 0;
               margin-left: 0;
               margin-right: 0;
               }
               .column,
               .columns {
               width: auto !important;
               float: none !important;
               margin-left: 0;
               margin-right: 0;
               clear: both;
               padding: 0;
               }
               [class*="column"] + [class*="column"]:last-child {
               float: none;
               }
               }
               /* larger screens
               --------------------------------------------------------------- */
               @media screen and (min-width:1200px) {
               .wide .wrap {
               max-width: 1180px;
               }
               }
               /*
               /* block grids
               ===================================================================== */
               .bgrid-sixth .bgrid {
               width: 16.66667%;
               }
               .bgrid-fourth .bgrid {
               width: 25%;
               }
               .bgrid-third .bgrid {
               width: 33.33333%;
               }
               .bgrid-half .bgrid {
               width: 50%;
               }
               .bgrid {
               padding: 0;
               }
               /* Clearing for block grid columns. Allows columns with
               different heights to align properly.
               --------------------------------------------------------------------- */
               .first {
               clear: both;
               }
               /* first column in large(default) screen - for IE8 */
               .bgrid-sixth .bgrid:nth-child(6n+1),
               .bgrid-fourth .bgrid:nth-child(4n+1),
               .bgrid-third .bgrid:nth-child(3n+1),
               .bgrid-half .bgrid:nth-child(2n+1) {
               clear: both;
               }
               /* smaller screens
               --------------------------------------------------------------- */
               @media screen and (max-width:900px) {
               /* block grids for small screens */
               .s-bgrid-sixth .bgrid {
               width: 16.66667%;
               }
               .s-bgrid-fourth .bgrid {
               width: 25%;
               }
               .s-bgrid-third .bgrid {
               width: 33.33333%;
               }
               .s-bgrid-half .bgrid {
               width: 50%;
               }
               .s-bgrid-whole .bgrid {
               width: 100%;
               clear: both;
               }
               .first,
               [class*="bgrid-"] .bgrid:nth-child(n) {
               clear: none;
               }
               .s-bgrid-sixth .bgrid:nth-child(6n+1),
               .s-bgrid-fourth .bgrid:nth-child(4n+1),
               .s-bgrid-third .bgrid:nth-child(3n+1),
               .s-bgrid-half .bgrid:nth-child(2n+1) {
               clear: both;
               }
               }
               /* tablets
               --------------------------------------------------------------- */
               @media screen and (max-width:768px) {
               .tab-bgrid-sixth .bgrid {
               width: 16.66667%;
               }
               .tab-bgrid-fourth .bgrid {
               width: 25%;
               }
               .tab-bgrid-third .bgrid {
               width: 33.33333%;
               }
               .tab-bgrid-half .bgrid {
               width: 50%;
               }
               .tab-bgrid-whole .bgrid {
               width: 100%;
               clear: both;
               }
               .first,
               [class*="tab-bgrid-"] .bgrid:nth-child(n) {
               clear: none;
               }
               .tab-bgrid-sixth .bgrid:nth-child(6n+1),
               .tab-bgrid-fourth .bgrid:nth-child(4n+1),
               .tab-bgrid-third .bgrid:nth-child(3n+1),
               .tab-bgrid-half .bgrid:nth-child(2n+1) {
               clear: both;
               }
               }
               /* mobile devices
               --------------------------------------------------------------- */
               @media screen and (max-width:600px) {
               .mob-bgrid-sixth .bgrid {
               width: 16.66667%;
               }
               .mob-bgrid-fourth .bgrid {
               width: 25%;
               }
               .mob-bgrid-third .bgrid {
               width: 33.33333%;
               }
               .mob-bgrid-half .bgrid {
               width: 50%;
               }
               .mob-bgrid-whole .bgrid {
               width: 100%;
               clear: both;
               }
               .first,
               [class*="mob-bgrid-"] .bgrid:nth-child(n) {
               clear: none;
               }
               .mob-bgrid-sixth .bgrid:nth-child(6n+1),
               .mob-bgrid-fourth .bgrid:nth-child(4n+1),
               .mob-bgrid-third .bgrid:nth-child(3n+1),
               .mob-bgrid-half .bgrid:nth-child(2n+1) {
               clear: both;
               }
               }
               /* stack on small mobile devices
               --------------------------------------------------------------- */
               @media screen and (max-width:400px) {
               .stack .bgrid {
               width: auto !important;
               float: none !important;
               margin-left: 0;
               margin-right: 0;
               clear: both;
               }
               }
               /*
               /* MISC
               ===================================================================== */
               /* Clearing - (http://nicolasgallagher.com/micro-clearfix-hack/
               --------------------------------------------------------------------- */
               .group:before,
               .group:after {
               content: "";
               display: table;
               }
               .group:after {
               clear: both;
               }
               /* Misc Helper Styles
               --------------------------------------------------------------------- */
               .hide {
               display: none;
               }
               .invisible {
               visibility: hidden;
               }
               .antialiased {
               -webkit-font-smoothing: antialiased;
               -moz-osx-font-smoothing: grayscale;
               }
               .remove-bottom {
               margin-bottom: 0;
               }
               .half-bottom {
               margin-bottom: 15px !important;
               }
               .add-bottom {
               margin-bottom: 30px !important;
               }
               .no-border {
               border: none;
               }
               .text-center {
               text-align: center;
               }
               .text-left {
               text-align: left;
               }
               .text-right {
               text-align: right;
               }
               .pull-left {
               float: left;
               }
               .pull-right {
               float: right;
               }
               .align-center {
               margin-left: auto;
               margin-right: auto;
               text-align: center;
               }
               @import url("fonts.css");
               @import url("font-awesome/css/font-awesome.min.css");
               html,
               body {
               height: 100%;
               }
               html {
               background: #161415 url(../images/bg.jpg) no-repeat center center fixed;
               -webkit-background-size: cover !important;
               -moz-background-size: cover !important;
               background-size: cover !important;
               }
               body {
               font: 15px/30px "montserrat-regular", sans-serif;
               font-weight: normal;
               color: #575859 !important;
               }
               /* links
               ---------------------------------------------------------------------- */
               a,
               a:visited {
               outline: none;
               color: #fbca08;
               -moz-transition: all 0.3s ease-in-out;
               -o-transition: all 0.3s ease-in-out;
               -webkit-transition: all 0.3s ease-in-out;
               -ms-transition: all 0.3s ease-in-out;
               transition: all 0.3s ease-in-out;
               }
               a:hover,
               a:focus {
               color: white;
               }
               /* Typography
               --------------------------------------------------------------------- */
               h1,
               h2,
               h3,
               h4,
               h5,
               h6 {
               font-family: "montserrat-bold", sans-serif;
               color: #575859;
               font-style: normal;
               text-rendering: optimizeLegibility;
               margin: 18px 0 15px;
               }
               h1 a,
               h2 a,
               h3 a,
               h4 a,
               h5 a,
               h6 a {
               font-weight: inherit;
               }
               h1 {
               font-size: 30px;
               line-height: 36px;
               margin-top: 0;
               letter-spacing: -1px;
               }
               h2 {
               font-size: 24px;
               line-height: 30px;
               }
               h3 {
               font-size: 20px;
               line-height: 30px;
               }
               h4 {
               font-size: 17px;
               line-height: 30px;
               }
               h5 {
               font-size: 14px;
               line-height: 30px;
               margin-top: 15px;
               text-transform: uppercase;
               letter-spacing: 1px;
               }
               h6 {
               font-size: 13px;
               line-height: 30px;
               margin-top: 15px;
               text-transform: uppercase;
               letter-spacing: 1px;
               }
               p {
               margin: 15px 0 15px 0;
               }
               p img {
               margin: 0;
               }
               p.lead {
               font: 17px/33px "montserrat-regular", sans-serif;
               color: #707273;
               }
               em {
               font: 15px/30px "montserrat-regular", sans-serif;
               font-style: normal;
               }
               strong,
               b {
               font: 15px/30px "montserrat-bold", sans-serif;
               font-weight: normal;
               }
               small {
               font-size: 11px;
               line-height: inherit;
               }
               blockquote {
               margin: 18px 0px;
               padding-left: 40px;
               position: relative;
               }
               blockquote:before {
               content: "\201C";
               opacity: 0.45;
               font-size: 80px;
               line-height: 0px;
               margin: 0;
               font-family: arial, sans-serif;
               position: absolute;
               top: 30px;
               left: 0;
               }
               blockquote p {
               font-family: georgia, serif;
               font-style: italic;
               padding: 0;
               font-size: 18px;
               line-height: 30px;
               }
               blockquote cite {
               display: block;
               font-size: 12px;
               font-style: normal;
               line-height: 18px;
               }
               blockquote cite:before {
               content: "\2014 \0020";
               }
               blockquote cite a,
               blockquote cite a:visited {
               color: #707273;
               border: none;
               }
               abbr {
               font-family: "montserrat-bold", serif;
               font-variant: small-caps;
               text-transform: lowercase;
               letter-spacing: .5px;
               color: #7d7e80;
               }
               pre,
               code {
               font-family: Consolas, "Andale Mono", Courier, "Courier New", monospace;
               }
               pre {
               white-space: pre;
               white-space: pre-wrap;
               word-wrap: break-word;
               }
               code {
               padding: 3px;
               background: #ECF0F1;
               color: #707273;
               border-radius: 3px;
               }
               del {
               text-decoration: line-through;
               }
               abbr[title],
               dfn[title] {
               border-bottom: 1px dotted;
               cursor: help;
               }
               mark {
               background: #FFF49B;
               color: #000;
               }
               hr {
               border: solid #fbca08;
               border-width: 1px 0 0;
               clear: both;
               margin: 23px 0 12px;
               height: 0;
               }
               /* Lists
               --------------------------------------------------------------------- */
               ul,
               ol {
               margin-top: 15px;
               margin-bottom: 15px;
               }
               ul {
               list-style: disc;
               margin-left: 17px;
               }
               dl {
               margin: 0 0 15px 0;
               }
               dt {
               margin: 0;
               color: #fbca08;
               }
               dd {
               margin: 0 0 0 20px;
               }
               /* Floated image
               --------------------------------------------------------------------- */
               img.pull-right {
               margin: 12px 0px 0px 18px;
               }
               img.pull-left {
               margin: 12px 18px 0px 0px;
               }
               /* Style Placeholder Text
               --------------------------------------------------------------------- */
               ::-webkit-input-placeholder {
               color: black;
               }
               :-moz-placeholder {
               /* Firefox 18- */
               color: black;
               }
               ::-moz-placeholder {
               /* Firefox 19+ */
               color: black;
               }
               :-ms-input-placeholder {
               color: black;
               }
               .placeholder {
               color: black !important;
               }
               /*
               /* 03. =preloader
               /* =================================================================== */
               #preloader {
               position: fixed;
               top: 0;
               left: 0;
               right: 0;
               bottom: 0;
               background: black;
               z-index: 9999999;
               height: 100%;
               }
               .no-js #preloader,
               .oldie #preloader,
               .ie9 #preloader {
               display: none;
               }
               #loader {
               position: absolute;
               left: 50%;
               top: 50%;
               width: 60px;
               height: 60px;
               margin: -30px 0 0 -30px;
               padding: 0;
               }
               #loader:before {
               content: "";
               border-top: 11px solid rgba(255, 255, 255, 0.2);
               border-right: 11px solid rgba(255, 255, 255, 0.2);
               border-bottom: 11px solid rgba(255, 255, 255, 0.2);
               border-left: 11px solid #fbca08;
               -webkit-animation: load 1.1s infinite linear;
               animation: load 1.1s infinite linear;
               display: block;
               border-radius: 50%;
               width: 60px;
               height: 60px;
               }
               @-webkit-keyframes load {
               0% {
               -webkit-transform: rotate(0deg);
               transform: rotate(0deg);
               }
               100% {
               -webkit-transform: rotate(360deg);
               transform: rotate(360deg);
               }
               }
               @keyframes load {
               0% {
               -webkit-transform: rotate(0deg);
               transform: rotate(0deg);
               }
               100% {
               -webkit-transform: rotate(360deg);
               transform: rotate(360deg);
               }
               }
               /*
               /* 04. forms
               /* =================================================================== */
               form {
               margin-bottom: 24px;
               }
               fieldset {
               margin: 0 0 24px 0;
               padding: 0;
               border: none;
               }
               input,
               button {
               -webkit-font-smoothing: antialiased;
               }
               input[type="text"],
               input[type="password"],
               input[type="email"],
               textarea,
               select {
               display: block;
               padding: 12px 15px;
               margin: 0 0 12px 0;
               border: 0;
               outline: none;
               vertical-align: middle;
               color: #a3a4a6;
               font-family: "montserrat-regular", sans-serif;
               font-size: 14px;
               line-height: 24px;
               border-radius: 3px;
               max-width: 100%;
               background: transparent;
               border: 3px solid #a9aaab;
               }
               textarea {
               min-height: 162px;
               }
               input[type="text"]:focus,
               input[type="password"]:focus,
               input[type="email"]:focus,
               textarea:focus {
               background: white;
               }
               label,
               legend {
               font: 15px/30px "montserrat-bold", sans-serif;
               margin: 12px 0;
               color: #252525;
               display: block;
               }
               label span,
               legend span {
               color: #575859;
               font: 15px/30px "montserrat-bold", serif;
               }
               input[type="checkbox"],
               input[type="radio"] {
               font-size: 15px;
               color: #575859;
               }
               input[type="checkbox"] {
               display: inline;
               }
               /*
               /* 05. =content styles
               /* =================================================================== */
               .browserupgrade {
               text-align: center;
               padding: 15px 30px;
               background: white;
               }
               #content-wrap {
               min-height: 100%;
               padding-top: 1%;
               }
               @media only screen and (max-width:900px) {
               #content-wrap {
               padding-top: 9%;
               }
               }
               @media only screen and (max-width:768px) {
               #content-wrap {
               padding-top: 13%;
               }
               }
               @media only screen and (max-width:600px) {
               #content-wrap {
               min-height: auto;
               padding-top: 60px;
               }
               }
               @media only screen and (max-width:400px) {
               #content-wrap {
               padding-top: 54px;
               }
               }
               /* main
               ------------------------------------------ */
               main.row {
               max-width: 700px;
               }
               main {
               text-align: center;
               }
               main::after {
               content: "";
               display: block;
               height: 150px;
               }
               main h1 {
               font: 38px/1.2em "montserrat-bold", sans-serif;
               color: #0732A2;
               margin-bottom: 12px;
               padding: 0;
               }
               main p {
               font: 17px/36px "montserrat-regular", sans-serif;
               color: #fff;
               margin-bottom: 18px;
               padding: 0;
               }
               main hr {
               border: solid #0732A2;
               border-width: 5px 0 0;
               margin: 19px auto 12px;
               height: 0;
               width: 100px;
               }
               main .site-header .logo {
               display: inline-block;
               vertical-align: middle;
               margin: 0 0 36px 0;
               padding: 0;
               }
               main .site-header .logo a {
               display: block;
               margin: 0;
               padding: 0;
               border: none;
               outline: none;
               font: 0/0 a;
               text-shadow: none;
               color: transparent;
               width: 80px;
               height: 80px;
               background: url("https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/under-construction/rbd-logo.png") no-repeat;
               background-size: contain;
               }
               @media only screen and (max-width:900px) {
               main.row {
               width: 85%;
               }
               main h1 {
               font: 35px/1.2em "montserrat-bold", sans-serif;
               }
               main p {
               font: 16px/30px "montserrat-regular", sans-serif;
               }
               main .site-header .logo {
               margin: 0 0 30px 0;
               }
               /* main .site-header .logo a {
               width: 104px;
               height: 72px;
               } */
               }
               @media only screen and (max-width:768px) {
               main.row {
               width: 92%;
               }
               main h1 {
               font: 32px/1.2em "montserrat-bold", sans-serif;
               }
               main p {
               font: 15px/30px "montserrat-regular", sans-serif;
               }
               main .site-header .logo {
               margin: 0 0 24px 0;
               }
               }
               @media only screen and (max-width:500px) {
               main.row {
               width: 96%;
               }
               main h1 {
               font: 30px/1.2em "montserrat-bold", sans-serif;
               }
               main p {
               line-height: 27px;
               }
               main::after {
               height: 78px;
               }
               }
               @media only screen and (max-width:400px) {
               main.row {
               width: 100%;
               }
               main h1 {
               font: 28px/1.2em "montserrat-bold", sans-serif;
               }
               main p {
               font: 14px/27px "montserrat-regular", sans-serif;
               }
               /* main .site-header .logo a {
               width: 95px;
               height: 66px;
               } */
               }
               /* counter
               ------------------------------------------ */
               #counter {
               width: 90%;
               color: white;
               text-align: center;
               margin: 18px auto 0;
               }
               #counter span {
               font: 62px/1em "montserrat-bold", sans-serif;
               display: block;
               padding: 12px 0 30px;
               min-width: 100%;
               float: left;
               }
               #counter span em {
               font: 11px/18px "montserrat-regular", sans-serif;
               text-transform: uppercase;
               letter-spacing: 2px;
               margin-top: 3px;
               display: block;
               color: rgba(255, 255, 255, 0.6);
               }
               @media only screen and (max-width:900px) {
               #counter {
               width: 92%;
               }
               #counter span {
               font: 58px/1em "montserrat-bold", sans-serif;
               }
               }
               @media only screen and (max-width:768px) {
               #counter {
               width: 100%;
               }
               #counter span {
               font: 52px/1em "montserrat-bold", sans-serif;
               }
               }
               @media only screen and (max-width:600px) {
               #counter {
               margin-bottom: 24px;
               }
               #counter span {
               font: 48px/1em "montserrat-bold", sans-serif;
               }
               }
               @media only screen and (max-width:500px) {
               #counter {
               margin-bottom: 12px;
               }
               #counter span {
               font: 37px/1em "montserrat-bold", sans-serif;
               }
               #counter span em {
               font: 9px/18px "montserrat-regular", sans-serif;
               letter-spacing: 1.5px;
               }
               }
               @media only screen and (max-width:400px) {
               #counter span {
               font: 31px/1em "montserrat-bold", sans-serif;
               }
               #counter span em {
               font: 8px/18px "montserrat-regular", sans-serif;
               letter-spacing: 1px;
               }
               }
               .contact p {
               margin: 9px 0;
               }
               .contact h3 {
               margin-top: 24px;
               margin-bottom: 0;
               }
               /*
               /* 07. =footer
               /* =================================================================== */
               footer {
               clear: both;
               font: 12px/24px "montserrat-regular", sans-serif;
               background: #000;
               padding: 18px 30px;
               color: #303030;
               width: 100%;
               position: fixed;
               bottom: 0;
               left: 0;
               z-index: 999992;
               }
               footer a,
               footer a:visited {
               color: #525252;
               }
               footer a:hover,
               footer a:focus {
               color: #fff;
               }
               /* copyright
               ------------------------------------------ */
               .footer-copyright {
               margin: 0;
               padding: 0;
               float: left;
               color: #fbca08;
               }
               .footer-copyright li {
               display: inline-block;
               margin: 0;
               padding: 0;
               line-height: 24px;
               }
               .footer-copyright li::before {
               content: "|";
               padding-left: 6px;
               padding-right: 10px;
               color: #2c2c2c;
               }
               .footer-copyright li:first-child:before {
               display: none;
               }
               /* social links */
               .footer-social {
               font-size: 18px;
               margin: 0;
               padding: 0;
               text-shadow: 0px 1px 2px rgba(0, 0, 0, 0.8);
               float: right;
               }
               .footer-social li {
               display: inline-block;
               margin: 0 10px;
               padding: 0;
               }
               .footer-social li a {
               color: #fbca08;
               }
               .footer-social li a:hover {
               color: white;
               }
               @media only screen and (max-width:768px) {
               footer {
               padding-top: 24px;
               text-align: center;
               }
               .footer-copyright {
               float: none;
               }
               .footer-social {
               float: none;
               margin-bottom: 15px;
               }
               }
               @media only screen and (max-width:600px) {
               footer {
               position: static;
               padding-bottom: 30px;
               }
               .footer-copyright li {
               display: block;
               margin: 0;
               padding: 0;
               line-height: 24px;
               }
               .footer-copyright li::before {
               content: none;
               }
               }
               @media only screen and (max-width:400px) {
               .footer-social {
               font-size: 17px;
               }
               .footer-social li {
               margin: 0 6px;
               }
               }
               .fa:before{
               font-family: "Font Awesome 5 Pro", Bangla167, sans-serif;
               font-weight: 900;
               font-style: normal;
               text-align: center;
               font-size: 16px;
               }
            </style>
            <link rel="shortcut icon" href="favicon.png">
         </head>
         <body>
            <div id="content-wrap">
               <main class="row">
                  <header class="site-header">
                     <!-- <div class="logo">
                        <a href="">Royalty.</a>
                     </div> -->
                  </header>
                  <div id="main-content" class="twelve columns">
                  <img src="https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/under-construction/staytuned.png" style="width=100%"
                  />
                     <!-- <h1>Royalty is currently under maintenance.</h1> -->
                     <h3> Thanks for visiting us. Please check back again soon. Have a query? Shoot us an email to support@royaltybd.com
                     </h3>
                     <hr>
                  </div>
               </main>
            </div>
            <footer class="group">
               <ul class="footer-social">
                  <li><a href="{{ url('https://www.facebook.com/RoyaltyBD/') }}" class="facebook"><i class="bx bxl-facebook"></i></a></li>
                  <li><a href="{{ url('https://www.instagram.com/RoyaltyBD/') }}" class="instagram"><i class="bx bxl-instagram"></i></a></li>
                  <li> <a href="{{ url('https://twitter.com/RoyaltyBD') }}" class="twitter"><i class="bx bxl-twitter"></i></a></li>
                  <li><a href="{{ url('https://www.youtube.com/channel/UCKFicIPvXBA-_a04LNsurhA') }}" class="youtube"><i class="bx bxl-youtube"></i></a></li>
                  <li><a href="{{ url('https://www.linkedin.com/company/royalty-bangladesh/')}}" class="linkedin"><i class="bx bxl-linkedin"></i></a></li>
                  <li><a href="{{ url('https://www.snapchat.com/add/royalty.bd')}}" class="snapchat"><i class="bx bxl-snapchat"></i></a></li>
               </ul>
               <ul class="footer-copyright">
                  <li>&copy; Copyright 2020 Royalty Inc.</li>
               </ul>
            </footer>
            <div id="preloader">
               <div id="loader">
               </div>
            </div>
         </body>
      </html>