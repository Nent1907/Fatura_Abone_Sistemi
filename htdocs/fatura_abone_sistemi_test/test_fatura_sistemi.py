import pytest
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.common.keys import Keys
from selenium.webdriver.support.ui import Select
from selenium.webdriver.chrome.service import Service
from webdriver_manager.chrome import ChromeDriverManager
from webdriver_manager.firefox import GeckoDriverManager
from webdriver_manager.microsoft import EdgeChromiumDriverManager
import time
import os

class TestFaturaSistemi:
    @pytest.fixture(autouse=True)
    def setup(self):
        # Chrome driver'ı otomatik yönetim ile başlat
        service = Service(ChromeDriverManager().install())
        self.driver = webdriver.Chrome(service=service)
        self.driver.maximize_window()
        self.driver.implicitly_wait(10)
        
        # Oturum açma işlemi
        self.login()
        
        yield
        self.driver.quit()
        
    def login(self):
        """Test öncesi oturum açma"""
        self.driver.get("http://localhost/fatura_abone_sistemi/giris.html")
        try:
            abone_no = self.driver.find_element(By.ID, "abone_no")
            sifre = self.driver.find_element(By.NAME, "sifre")
            
            abone_no.send_keys("123456")
            sifre.send_keys("12345")
            
            submit_btn = self.driver.find_element(By.CSS_SELECTOR, "button[type='submit']")
            submit_btn.click()
            
            # Ana sayfanın yüklenmesini bekle
            WebDriverWait(self.driver, 10).until(
                EC.presence_of_element_located((By.CLASS_NAME, "container"))
            )
        except Exception as e:
            print(f"Login error: {str(e)}")

    def test_web_elementleri_bulma(self):
        """Test 1: Web elementlerini bulma yöntemlerinden kaçı kullanılmış?"""
        self.driver.get("http://localhost/fatura_abone_sistemi/giris.html")
        
        # Metin girme (send_keys())
        abone_no = self.driver.find_element(By.ID, "abone_no")
        abone_no.send_keys("123456")
        
        # Metin alma (text)
        baslik = self.driver.find_element(By.TAG_NAME, "h1").text
        assert "Giriş" in baslik

    def test_checkbox_radio(self):
        """Test 2: Checkbox ve radio button işlemleri"""
        self.driver.get("http://localhost/fatura_abone_sistemi/kayit.php")
        time.sleep(1)  # Sayfanın yüklenmesi için kısa bekleme
        
        try:
            # Radio button seçimi
            radio_erkek = WebDriverWait(self.driver, 10).until(
                EC.presence_of_element_located((By.CSS_SELECTOR, "input[value='erkek']"))
            )
            radio_erkek.click()
            assert radio_erkek.is_selected()
        except Exception as e:
            pytest.fail(f"Radio button error: {str(e)}")

    def test_dropdown(self):
        """Test 3: Dropdown menüleri yönetme"""
        self.driver.get("http://localhost/fatura_abone_sistemi/bakiye_ekle.php")
        time.sleep(1)  # Sayfanın yüklenmesi için kısa bekleme
        
        try:
            # Dropdown seçimi
            banka_select = WebDriverWait(self.driver, 10).until(
                EC.presence_of_element_located((By.ID, "banka"))
            )
            select = Select(banka_select)
            select.select_by_value("ziraat-mastercard")
            
            selected_option = select.first_selected_option
            assert "Ziraat" in selected_option.text
        except Exception as e:
            pytest.fail(f"Dropdown error: {str(e)}")

    def test_wait_methods(self):
        """Test 4: Wait kullanımları"""
        self.driver.get("http://localhost/fatura_abone_sistemi/main_page.php")
        
        try:
            # Explicit wait ve WebDriverWait kullanımı
            wait = WebDriverWait(self.driver, 10)
            
            # Kartların yüklenmesini bekle
            cards = wait.until(
                EC.presence_of_all_elements_located((By.CLASS_NAME, "card"))
            )
            assert len(cards) > 0
            
            # İlk kartın içindeki butonu bekle
            elektrik_btn = wait.until(
                EC.element_to_be_clickable((By.CSS_SELECTOR, ".card button"))
            )
            assert elektrik_btn.is_displayed()
            
        except Exception as e:
            pytest.fail(f"Wait error: {str(e)}")

    def test_frame_js_element_state(self):
        """Test 5: Frame, JS ve element durumu kontrolleri"""
        self.driver.get("http://localhost/fatura_abone_sistemi/main_page.php")
        time.sleep(1)  # Sayfanın yüklenmesi için kısa bekleme
        
        try:
            # iframe kontrolü
            iframe = WebDriverWait(self.driver, 10).until(
                EC.presence_of_element_located((By.TAG_NAME, "iframe"))
            )
            
            # iframe'e geç
            self.driver.switch_to.frame(iframe)
            
            # iframe içinde bir element kontrolü
            footer_text = self.driver.find_element(By.CSS_SELECTOR, "div").text
            assert "Google" in footer_text
            
            # Ana sayfaya geri dön
            self.driver.switch_to.default_content()
            
            # JavaScript ile scroll
            self.driver.execute_script("window.scrollTo(0, document.body.scrollHeight);")
            
            # Çıkış butonunun durumunu kontrol et
            logout_btn = WebDriverWait(self.driver, 10).until(
                EC.presence_of_element_located((By.CLASS_NAME, "logout-btn"))
            )
            assert logout_btn.is_enabled()
            assert logout_btn.is_displayed()
            
        except Exception as e:
            pytest.fail(f"Frame/JS error: {str(e)}")

    def test_cross_browser(self):
        """Test 6: Farklı tarayıcılarda test"""
        test_url = "http://localhost/fatura_abone_sistemi/giris.html"
        
        try:
            # Chrome testi
            self.driver.get(test_url)
            assert "Giriş" in self.driver.title
            
            # Firefox testi
            firefox_service = Service(GeckoDriverManager().install())
            firefox_driver = webdriver.Firefox(service=firefox_service)
            try:
                firefox_driver.get(test_url)
                assert "Giriş" in firefox_driver.title
            finally:
                firefox_driver.quit()
            
            # Edge testi
            edge_service = Service(EdgeChromiumDriverManager().install())
            edge_driver = webdriver.Edge(service=edge_service)
            try:
                edge_driver.get(test_url)
                assert "Giriş" in edge_driver.title
            finally:
                edge_driver.quit()
                
        except Exception as e:
            pytest.fail(f"Cross browser error: {str(e)}")

    def test_screenshot(self):
        """Test 7: Screenshot alma"""
        self.driver.get("http://localhost/fatura_abone_sistemi/main_page.php")
        time.sleep(1)  # Sayfanın yüklenmesi için kısa bekleme
        
        try:
            # Screenshots klasörünü oluştur
            screenshot_dir = "fatura_abone_sistemi_test/screenshots"
            os.makedirs(screenshot_dir, exist_ok=True)
            
            # Screenshot al
            screenshot_path = os.path.join(screenshot_dir, "main_page.png")
            self.driver.save_screenshot(screenshot_path)
            assert os.path.exists(screenshot_path)
            
        except Exception as e:
            pytest.fail(f"Screenshot error: {str(e)}")

    def test_logging(self):
        """Test 8: Logging"""
        import logging
        
        try:
            # Log klasörünü oluştur
            log_dir = "fatura_abone_sistemi_test/logs"
            os.makedirs(log_dir, exist_ok=True)
            
            # Logging konfigürasyonu
            log_file = os.path.join(log_dir, "test.log")
            logging.basicConfig(
                filename=log_file,
                level=logging.INFO,
                format='%(asctime)s - %(levelname)s - %(message)s',
                force=True  # Önceki logger'ı override et
            )
            
            logger = logging.getLogger(__name__)
            
            self.driver.get("http://localhost/fatura_abone_sistemi/giris.html")
            logger.info("Giriş sayfası başarıyla yüklendi")
            
            # Test adımları
            abone_no = self.driver.find_element(By.ID, "abone_no")
            abone_no.send_keys("123456")
            logger.info("Abone numarası girildi")
            
            sifre = self.driver.find_element(By.NAME, "sifre")  # ID yerine NAME kullan
            sifre.send_keys("12345")
            logger.info("Şifre girildi")
            
            # Submit butonunu bul ve tıkla
            submit_btn = self.driver.find_element(By.CSS_SELECTOR, "button[type='submit']")
            submit_btn.click()
            logger.info("Giriş butonu tıklandı")
            
            # Log dosyasının oluştuğunu kontrol et
            assert os.path.exists(log_file), "Log dosyası oluşturulmadı"
            
            # Log içeriğini kontrol et
            with open(log_file, 'r') as f:
                log_content = f.read()
                assert "Giriş sayfası başarıyla yüklendi" in log_content, "Log içeriği doğru değil"
            
        except Exception as e:
            logging.error(f"Hata oluştu: {str(e)}")
            raise  # Hatayı yeniden fırlat

    def test_parallel(self):
        """Test 10: Parallel test çalıştırma"""
        # pytest-xdist ile parallel test çalıştırma
        # Terminal komutu: pytest -n auto test_fatura_sistemi.py
        pass 