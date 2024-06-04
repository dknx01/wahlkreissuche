/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)

import '../css/global.scss';
import '../css/app.css';
// Need jQuery? Install it with "yarn add jquery", then uncomment to import it.
import $ from 'jquery';
//const $ = require('jquery');
// this "modifies" the jquery module: adding behavior to it
// the bootstrap module doesn't export/return anything
require('bootstrap/dist/js/bootstrap');
require('@fortawesome/fontawesome-free/js/all.min');

// or you can include specific pieces
// require('bootstrap/js/dist/tooltip');
// require('bootstrap/js/dist/popover');
//require('bootstrap/js/dist/util');
//require('bootstrap/js/dist/toast');
// require('bootstrap/js/dist/tab');
// require('bootstrap/js/dist/collapse')
require('popper.js');
$(document).ready(function() {
    $('[data-toggle="popover"]').popover();
});