"""Tes autentikasi: login Penyewa & Vendor (sukses dan gagal)."""

import pytest

import config
from pages.login_page import LoginPage, VendorLoginPage


@pytest.mark.auth
@pytest.mark.smoke
def test_user_login_berhasil(driver, base_url):
    page = LoginPage(driver, base_url).open()
    page.login(config.USER_EMAIL, config.USER_PASSWORD)

    page.wait_for_path("/user/dashboard")
    assert page.current_path.startswith("/user/dashboard")


@pytest.mark.auth
def test_user_login_gagal_password_salah(driver, base_url):
    page = LoginPage(driver, base_url).open()
    page.login(config.USER_EMAIL, "password-salah")

    # Tetap di halaman login dan ada pesan error.
    assert page.current_path.startswith("/login")
    assert page.has_error()


@pytest.mark.auth
@pytest.mark.smoke
def test_vendor_login_berhasil(driver, base_url):
    page = VendorLoginPage(driver, base_url).open()
    page.login(config.VENDOR_EMAIL, config.VENDOR_PASSWORD)

    page.wait_for_path("/vendor/dashboard")
    assert page.current_path.startswith("/vendor/dashboard")
