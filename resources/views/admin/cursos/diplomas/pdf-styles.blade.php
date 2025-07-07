@import url("https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Roboto:wght@300;400;500&display=swap");

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body { 
    margin: 0; 
    padding: 0; 
    font-family: "Roboto", sans-serif;
    background: white;
}

.page { 
    page-break-after: always; 
    page-break-inside: avoid;
    height: 100vh;
    width: 100%;
    position: relative;
    display: block;
    clear: both;
    margin: 0;
    padding: 0;
}

.page:last-child { 
    page-break-after: avoid; 
}

.diploma {
    background: linear-gradient(145deg, #f8f9fa 0%, #ffffff 100%);
    width: 100%;
    height: 100vh;
    position: relative;
    border-radius: 20px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    border: 8px solid #2c3e50;
    page-break-inside: avoid;
}

.diploma::before {
    content: "";
    position: absolute;
    top: 20px;
    left: 20px;
    right: 20px;
    bottom: 20px;
    border: 2px solid #e74c3c;
    border-radius: 15px;
    pointer-events: none;
}

.diploma::after {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image: 
        radial-gradient(circle at 20% 20%, rgba(231, 76, 60, 0.05) 0%, transparent 50%),
        radial-gradient(circle at 80% 80%, rgba(52, 152, 219, 0.05) 0%, transparent 50%);
    pointer-events: none;
}

.diploma-content {
    position: relative;
    z-index: 1;
    padding: 40px;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    align-items: center;
    text-align: center;
    min-height: 100vh;
}

.logo {
    font-family: "Playfair Display", serif;
    font-size: 2.5rem;
    font-weight: 900;
    color: #2c3e50;
    margin-bottom: 10px;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
}

.institution {
    font-size: 1.2rem;
    color: #7f8c8d;
    font-weight: 300;
    letter-spacing: 2px;
    text-transform: uppercase;
}

.diploma-title {
    font-family: "Playfair Display", serif;
    font-size: 3rem;
    font-weight: 700;
    color: #2c3e50;
    margin: 40px 0;
    line-height: 1.2;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
}

.diploma-text {
    font-size: 1.4rem;
    color: #34495e;
    line-height: 1.6;
    margin-bottom: 30px;
    max-width: 800px;
}

.course-name {
    font-family: "Playfair Display", serif;
    font-size: 2rem;
    font-weight: 700;
    color: #e74c3c;
    margin: 20px 0;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
}

.course-info {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin: 30px 0;
    width: 100%;
    max-width: 800px;
}

.info-item {
    text-align: center;
    padding: 20px;
    background: rgba(255, 255, 255, 0.9);
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(231, 76, 60, 0.1);
}

.info-label {
    font-size: 0.9rem;
    color: #7f8c8d;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 5px;
}

.info-value {
    font-size: 1.1rem;
    color: #2c3e50;
    font-weight: 500;
}

.signature-section {
    margin-top: 40px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 40px;
    width: 100%;
    max-width: 800px;
}

.signature-item {
    text-align: center;
    padding: 20px;
}

.signature-line {
    width: 200px;
    height: 2px;
    background: #2c3e50;
    margin: 20px auto;
}

.signature-name {
    font-size: 1.1rem;
    color: #2c3e50;
    font-weight: 500;
}

.signature-title {
    font-size: 0.9rem;
    color: #7f8c8d;
    margin-top: 5px;
}

.diploma-date {
    position: absolute;
    bottom: 40px;
    right: 60px;
    font-size: 1rem;
    color: #7f8c8d;
    font-style: italic;
}

.diploma-details {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
    margin: 30px 0;
    width: 100%;
}

.detail-section {
    background: rgba(255, 255, 255, 0.8);
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.detail-title {
    font-size: 1.2rem;
    color: #2c3e50;
    font-weight: 600;
    margin-bottom: 15px;
    border-bottom: 2px solid #e74c3c;
    padding-bottom: 5px;
}

.detail-item {
    margin-bottom: 12px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.detail-label {
    font-size: 0.9rem;
    color: #7f8c8d;
    font-weight: 500;
}

.detail-value {
    font-size: 1rem;
    color: #2c3e50;
    font-weight: 600;
}

.verification-section {
    text-align: center;
    margin: 40px 0;
}

.qr-code {
    width: 120px;
    height: 120px;
    background: #f8f9fa;
    border: 2px solid #2c3e50;
    border-radius: 10px;
    margin: 0 auto 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    color: #7f8c8d;
}

.verification-text {
    font-size: 0.9rem;
    color: #7f8c8d;
    margin-bottom: 10px;
}

.verification-url {
    font-size: 0.8rem;
    color: #3498db;
    font-weight: 500;
}

.additional-info {
    background: rgba(52, 152, 219, 0.1);
    padding: 20px;
    border-radius: 15px;
    margin: 30px 0;
}

.additional-title {
    font-size: 1.1rem;
    color: #2c3e50;
    font-weight: 600;
    margin-bottom: 15px;
    text-align: center;
}

.additional-text {
    font-size: 0.9rem;
    color: #34495e;
    line-height: 1.6;
    text-align: justify;
}

.diploma-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 30px;
}

.footer-left {
    text-align: left;
}

.footer-right {
    text-align: right;
}

.footer-text {
    font-size: 0.8rem;
    color: #7f8c8d;
}

.decorative-corner {
    position: absolute;
    width: 60px;
    height: 60px;
    border: 3px solid #e74c3c;
}

.corner-tl {
    top: 40px;
    left: 40px;
    border-right: none;
    border-bottom: none;
}

.corner-tr {
    top: 40px;
    right: 40px;
    border-left: none;
    border-bottom: none;
}

.corner-bl {
    bottom: 40px;
    left: 40px;
    border-right: none;
    border-top: none;
}

.corner-br {
    bottom: 40px;
    right: 40px;
    border-left: none;
    border-top: none;
} 