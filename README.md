<div id="top"></div>

[![Contributors][contributors-shield]][contributors-url]
[![Forks][forks-shield]][forks-url]
[![Stargazers][stars-shield]][stars-url]
[![Issues][issues-shield]][issues-url]
[![GPL License][license-shield]][license-url]
[![LinkedIn][linkedin-shield]][linkedin-url]



<!-- PROJECT LOGO -->
<br />
<div align="center">
  <a href="https://github.com/swadmahfuz/Certificate-Verification-System">
    <img src="images/logo.png" alt="Logo" width="80" height="80">
  </a>

<h3 align="center">Certificate Verification Web Application (Laravel)</h3>
<br />

  <p align="center">
    Developed by Swad Mahfuz (Asst. Manager - Sales & Ops, TÜV Austria). 
    <br />
    <a href="https://github.com/swadmahfuz/Certificate-Verification-System"><strong>Explore the project »</strong></a>
  </p>
</div>



<!-- TABLE OF CONTENTS -->
<details>
  <summary>Table of Contents</summary>
  <ol>
    <li>
      <a href="#about-the-project">About The Project</a>
      <ul>
        <li><a href="#built-with">Built With</a></li>
      </ul>
    </li>
    <li>
      <a href="#getting-started">Getting Started</a>
      <ul>
        <li><a href="#prerequisites">Prerequisites</a></li>
        <li><a href="#installation">Installation</a></li>
      </ul>
    </li>
    <li><a href="#usage">Usage</a></li>
    <li><a href="#contributing">Contributing</a></li>
    <li><a href="#license">License</a></li>
    <li><a href="#contact">Contact</a></li>
  </ol>
</details>



<!-- ABOUT THE PROJECT -->
## About The Project

[![Product Name Screen Shot][product-screenshot]](https://github.com/swadmahfuz/Certificate-Verification-System)
<br />
This project is made for maintaining and verifying certificates issued by an organization. Admins can add/edit/remove cetificate data from the dashboard and interested parties can verify the authenticity of the certificates. The project may be modified for verifying other things like product authenticity.

<strong>Main Features: </strong>
* Maintain Certificate Database.
* Certificate Verification
* Admin Dashboard
* Generate and download QR Code for verification. 

<p align="right">(<a href="#top">back to top</a>)</p>



### Built With

* Laravel 8.83.25
* PHP 7.4.30
* HTML
* CSS
* goqr.me API for QR Code Generation.

<p align="right">(<a href="#top">back to top</a>)</p>



<!-- GETTING STARTED -->
## Getting Started

To get a local copy up and running follow these simple example steps.

### Prerequisites

This project is built using Laravel. You may need to have installed php composer to run the project.

### Installation

1. Get Composer.
2. Clone the repo and resolve dependencies, if any.
   ```sh
   git clone https://github.com/swadmahfuz/Certificate-Verification-System.git
   ```
3. Import the sample .sql file to your DB and configure .env file as per DB Name.
4. Run the project
5. Register new user to login to dashboard. (Make sure registration route is enabled in web.php)

<stong>Notes</strong>

*  All required packages included in the project. No need to run ```composer install```  
*  ```php artisan serve``` command not required to run the project.
* Default Login Credentials included in sample DB: 'swad.mahfuz@gmail.com' , '123456789'
* Template for Bulk Import included in 'root/downloads' folder.

<p align="right">(<a href="#top">back to top</a>)</p>



<!-- USAGE EXAMPLES -->
## Usage

This project can be used for maintaining and verifying certificates issued by an organization. The project may be modified for verifying other things like product authenticity.


<p align="right">(<a href="#top">back to top</a>)</p>


<!-- LICENSE -->
## License

Distributed under the GPL-3.0 License. See `LICENSE` for more information.

<p align="right">(<a href="#top">back to top</a>)</p>



<!-- CONTACT -->
## Contact

Swad Ahmed Mahfuz - contact@swadmahfuz.com

Project Link: [https://github.com/swadmahfuz/Certificate-Verification-System](https://github.com/swadmahfuz/Certificate-Verification-System)

<p align="right">(<a href="#top">back to top</a>)</p>



<!-- MARKDOWN LINKS & IMAGES -->
<!-- https://www.markdownguide.org/basic-syntax/#reference-style-links -->
[contributors-shield]: https://img.shields.io/github/contributors/swadmahfuz/Certificate-Verification-System.svg?style=for-the-badge
[contributors-url]: https://github.com/swadmahfuz/Certificate-Verification-System/graphs/contributors
[forks-shield]: https://img.shields.io/github/forks/swadmahfuz/Certificate-Verification-System.svg?style=for-the-badge
[forks-url]: https://github.com/swadmahfuz/Certificate-Verification-System/network/members
[stars-shield]: https://img.shields.io/github/stars/swadmahfuz/Certificate-Verification-System.svg?style=for-the-badge
[stars-url]: https://github.com/swadmahfuz/Certificate-Verification-System/stargazers
[issues-shield]: https://img.shields.io/github/issues/swadmahfuz/Certificate-Verification-System.svg?style=for-the-badge
[issues-url]: https://github.com/swadmahfuz/Certificate-Verification-System/issues
[license-shield]: https://img.shields.io/github/license/swadmahfuz/Certificate-Verification-System.svg?style=for-the-badge
[license-url]: https://github.com/swadmahfuz/Certificate-Verification-System/blob/master/LICENSE
[linkedin-shield]: https://img.shields.io/badge/-LinkedIn-black.svg?style=for-the-badge&logo=linkedin&colorB=555
[linkedin-url]: https://linkedin.com/in/swad-mahfuz/
[product-screenshot]: images/screenshot.png
