"""Konfigurasi terpusat untuk suite Selenium, dibaca dari environment / .env."""

import os

from dotenv import load_dotenv

# Muat .env yang berada di folder selenium-tests (jika ada).
load_dotenv(os.path.join(os.path.dirname(__file__), ".env"))


def _bool(value: str, default: bool = True) -> bool:
    if value is None:
        return default
    return value.strip().lower() in ("1", "true", "yes", "on")


BASE_URL: str = os.getenv("SELENIUM_BASE_URL", "http://localhost:8000").rstrip("/")
HEADLESS: bool = _bool(os.getenv("SELENIUM_HEADLESS"), default=True)

USER_EMAIL: str = os.getenv("SELENIUM_USER_EMAIL", "user@selenium.test")
USER_PASSWORD: str = os.getenv("SELENIUM_USER_PASSWORD", "password")
VENDOR_EMAIL: str = os.getenv("SELENIUM_VENDOR_EMAIL", "vendor@selenium.test")
VENDOR_PASSWORD: str = os.getenv("SELENIUM_VENDOR_PASSWORD", "password")

# Berapa lama (detik) menunggu elemen muncul sebelum dianggap gagal.
DEFAULT_TIMEOUT: int = 10
