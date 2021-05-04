// import './_noir.scss';
// import {Login} from './Login';
// import {Stars} from "./stars";
// import {MovieInfo} from "./MovieInfo";
import {TimeClock} from "./TimeClock";
import $ from 'jquery';


$(document).ready(function() {
    new TimeClock('#timeclock');
});