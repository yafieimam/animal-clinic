// Load plugins
import helper from "./helper";
import axios from "axios";
import * as Popper from "@popperjs/core";
import dom from "@left4code/tw-starter/dist/js/dom";
import Inputmask from "inputmask";
import tomselect from "tom-select";
import Swal from 'sweetalert2'
import toastify from 'toastify-js'
import select from 'select2'
import Jspreadsheet from 'jspreadsheet-ce'
import Papaparse from 'papaparse'
import Dayjs from "dayjs";
import Accounting from "accounting-js";
import Smartwizard from "smartwizard";
import Marquee from "jquery.marquee";
import Jstree from "jstree";
import Html2canvas from "html2canvas";
// Set plugins globally
window.helper = helper;
window.axios = axios;
window.Popper = Popper;
window.TomSelect = tomselect;
window.Swal = Swal;
window.Toastify = toastify;
window.select2 = select;
window.jexcel = Jspreadsheet;
window.Papa = Papaparse;
window.dayjs = Dayjs;
window.accounting = Accounting;
window.smartwizard = Smartwizard;
window.marquee = Marquee;
window.jstree = Jstree;
window.html2canvas = Html2canvas;

// window.$ = dom;
window.$ = window.jQuery = require('jquery');

require('pdfmake');
require('datatables.net-dt');
require('datatables.net-autofill-dt');
require('datatables.net-buttons/js/buttons.colVis.js');
require('datatables.net-buttons/js/buttons.html5.js');
require('datatables.net-buttons/js/buttons.print.js');
require('datatables.net-fixedcolumns-dt');
require('datatables.net-fixedheader-dt');
require('datatables.net-responsive-dt');


// CSRF token
let token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common["X-CSRF-TOKEN"] = token.content;
} else {
    console.error(
        "CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token"
    );
}
