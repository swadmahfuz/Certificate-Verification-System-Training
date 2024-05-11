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

<h3 align="center">🛡️ TUV Austria Training Certificate Verification System</h3>
<br />

  <p align="center">
    Developed by Swad Mahfuz - Assistant Manager (Sales & Operations), TÜV Austria (Bangladesh Office). 
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
<h2  align="center">🛡️ TUV Austria Training Certificate Verification System </h2>

<h3>📌 Overview </h3></br>

<p>This application is designed to address the persistent challenges of certificate forgery and inefficient manual verification processes that our organization, TUV Austria, has frequently encountered. The primary goal of this tool is to enhance operational efficiency and accuracy in the authentication of certificates and reports.</p>

<h3>🗂️ Key Features </h3></br>

* Database Management 📁: Maintains a comprehensive database of certificates and reports, streamlining the storage and retrieval process.
* QR Code Verification 📱: Enables clients and other stakeholders to verify the authenticity of certificates and reports swiftly using QR codes.
* TUV Austria Employee Dashboard 🖥️: Allows TUV Austria employees to efficiently manage certificate data, including adding, editing, and removing entries.
* Versatility 🔧: While primarily focused on certificates, the application’s architecture allows for future adaptations, such as verifying product authenticity or third-party reports.
</br>

<h3>🚀 Benefits </h3> </br>

* Time Efficiency ⏳: Significantly reduces the time spent by employees on manual verification processes.
* Security 🛑: Enhances the integrity of the verification process, helping to prevent fraud and unauthorized use of falsified documents.
* User-Friendly 🤝: Provides a user-friendly interface for both administrative tasks and verification processes.

<h3>🌟 Potential Expansions</h3></br>
<p>This project holds potential for further modifications to accommodate a broader range of verification needs, including but not limited to product authenticity and third-party reports. A complimenting app has also been developed for Inspection/Calibration Certificate Verification that uses the same database, which runs from a separate directory. </p>

App link: [https://github.com/swadmahfuz/Certificate-Verification-System-Inspection](https://github.com/swadmahfuz/Certificate-Verification-System-Inspection) 

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

Please note that the Inspection CVS application (separate application) must be installed in the same domain under a seprate sub-domain for the whole system to work. It is recommended to install Training CVS first and then the Inspection CVS.

This application shares a common database with the Inspection CVS and thus the .env file for both the applications should be configured in a way so that both Training CVS and Inspection CVS uses the same database name & credentials and SMTP credentials.

<stong>Notes</strong>

*  All required packages included in the project. No need to run ```composer install```  
*  ```php artisan serve``` command not required to run the project.
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

Swad Ahmed Mahfuz - contact@swadmahfuz.com, swad.mahfuz@gmail.com

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
