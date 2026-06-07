"""Fixture pytest: menyiapkan & membersihkan WebDriver Chrome untuk tiap tes."""

import datetime
import os

import pytest
from selenium import webdriver
from selenium.webdriver.chrome.options import Options

import config

SCREENSHOT_DIR = os.path.join(os.path.dirname(__file__), "screenshots")


@pytest.fixture
def driver():
    """Chrome WebDriver baru untuk setiap tes (Selenium Manager mengurus driver-nya)."""
    options = Options()
    if config.HEADLESS:
        options.add_argument("--headless=new")
    options.add_argument("--window-size=1400,1000")
    options.add_argument("--no-sandbox")
    options.add_argument("--disable-dev-shm-usage")
    options.add_argument("--disable-gpu")
    # Bahasa Indonesia agar teks yang di-assert konsisten.
    options.add_argument("--lang=id-ID")

    driver = webdriver.Chrome(options=options)
    driver.implicitly_wait(2)

    yield driver

    driver.quit()


@pytest.fixture
def base_url():
    return config.BASE_URL


def pytest_runtest_makereport(item, call):
    """Simpan screenshot otomatis ketika sebuah tes gagal — memudahkan debugging."""
    if call.when != "call" or not call.excinfo:
        return

    driver = item.funcargs.get("driver")
    if driver is None:
        return

    os.makedirs(SCREENSHOT_DIR, exist_ok=True)
    stamp = datetime.datetime.now().strftime("%Y%m%d-%H%M%S")
    path = os.path.join(SCREENSHOT_DIR, f"{item.name}-{stamp}.png")
    try:
        driver.save_screenshot(path)
        print(f"\n[screenshot] {path}")
    except Exception:  # noqa: BLE001 - screenshot bersifat best-effort
        pass
