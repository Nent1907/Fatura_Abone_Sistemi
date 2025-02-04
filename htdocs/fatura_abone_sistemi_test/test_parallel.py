import pytest
from selenium.webdriver.common.by import By
import time

def test_login_chrome(driver):
    """Chrome tarayıcısında test"""
    driver.get("http://localhost/fatura_abone_sistemi/giris.html")
    time.sleep(1)
    
    abone_no = driver.find_element(By.ID, "abone_no")
    sifre = driver.find_element(By.NAME, "sifre")
    
    abone_no.send_keys("123456")
    sifre.send_keys("12345")
    
    submit_btn = driver.find_element(By.CSS_SELECTOR, "button[type='submit']")
    submit_btn.click()
    
    time.sleep(2)
    assert "main_page" in driver.current_url

def test_login_firefox(firefox_driver):
    """Firefox tarayıcısında test"""
    firefox_driver.get("http://localhost/fatura_abone_sistemi/giris.html")
    time.sleep(1)
    
    abone_no = firefox_driver.find_element(By.ID, "abone_no")
    sifre = firefox_driver.find_element(By.NAME, "sifre")
    
    abone_no.send_keys("123456")
    sifre.send_keys("12345")
    
    submit_btn = firefox_driver.find_element(By.CSS_SELECTOR, "button[type='submit']")
    submit_btn.click()
    
    time.sleep(2)
    assert "main_page" in firefox_driver.current_url

def test_login_edge(edge_driver):
    """Edge tarayıcısında test"""
    edge_driver.get("http://localhost/fatura_abone_sistemi/giris.html")
    time.sleep(1)
    
    abone_no = edge_driver.find_element(By.ID, "abone_no")
    sifre = edge_driver.find_element(By.NAME, "sifre")
    
    abone_no.send_keys("123456")
    sifre.send_keys("12345")
    
    submit_btn = edge_driver.find_element(By.CSS_SELECTOR, "button[type='submit']")
    submit_btn.click()
    
    time.sleep(2)
    assert "main_page" in edge_driver.current_url 