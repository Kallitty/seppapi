import "./bootstrap";
import "../sass/app.scss"; // Import the compiled Sass file
import "../css/app.css"; // Import the CSS file

import axios from "axios";

axios.defaults.headers.common["X-CSRF-TOKEN"] = document
    .querySelector('meta[name="csrf-token"]')
    .getAttribute("content");
