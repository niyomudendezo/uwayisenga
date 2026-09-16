from pathlib import Path
from time import sleep
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC

BASE_URL = 'http://localhost/agrukrwanda'
OUTPUT = Path('/opt/lampp/htdocs/agrukrwanda/public/assets/images/screenshots')
OUTPUT.mkdir(parents=True, exist_ok=True)

options = Options()
options.binary_location = '/usr/bin/chromium'
options.add_argument('--headless=new')
options.add_argument('--no-sandbox')
options.add_argument('--disable-dev-shm-usage')
options.add_argument('--hide-scrollbars')
options.add_argument('--window-size=1440,1000')
options.add_argument('--force-device-scale-factor=1')

driver = webdriver.Chrome(service=Service('/usr/bin/chromedriver'), options=options)
driver.set_window_size(1440, 1000)
wait = WebDriverWait(driver, 12)

def capture(path, filename, pause=1.5):
    driver.get(BASE_URL + path)
    wait.until(lambda d: d.execute_script('return document.readyState') == 'complete')
    sleep(pause)
    driver.save_screenshot(str(OUTPUT / filename))

def login(email, password='Admin@1234'):
    driver.delete_all_cookies()
    driver.get(BASE_URL + '/login')
    wait.until(EC.visibility_of_element_located((By.NAME, 'email'))).send_keys(email)
    driver.find_element(By.NAME, 'password').send_keys(password)
    driver.find_element(By.CSS_SELECTOR, 'button[type="submit"]').click()
    wait.until(lambda d: '/dashboard' in d.current_url)
    sleep(1.5)

try:
    capture('/', '01-homepage.png', 2.5)
    capture('/login', '02-login.png')
    capture('/register', '03-registration.png')

    login('admin@agrukrwanda.rw')
    capture('/admin/dashboard', '04-admin-dashboard.png')

finally:
    driver.quit()

for image in sorted(OUTPUT.glob('*.png')):
    print(image)
