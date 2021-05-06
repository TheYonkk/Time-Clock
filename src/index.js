// import './_noir.scss';

import {TimeClock} from "./TimeClock";
import {Users} from "./Users";
import $ from 'jquery';


$(document).ready(function() {
    new TimeClock('#timeclock');
});

$(document).ready(function() {
    new Users('#users');
});
