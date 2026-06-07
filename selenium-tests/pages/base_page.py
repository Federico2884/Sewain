"""Kelas dasar Page Object: helper navigasi & penungguan elemen."""

from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.support.ui import WebDriverWait

import config


class BasePage:
    def __init__(self, driver, base_url: str | None = None):
        self.driver = driver
        self.base_url = (base_url or config.BASE_URL).rstrip("/")
        self.wait = WebDriverWait(driver, config.DEFAULT_TIMEOUT)

    def visit(self, path: str = "/"):
        self.driver.get(f"{self.base_url}{path}")
        return self

    @property
    def current_path(self) -> str:
        """Path URL saat ini tanpa domain (mis. '/user/dashboard')."""
        url = self.driver.current_url
        without_scheme = url.split("://", 1)[-1]
        slash = without_scheme.find("/")
        return without_scheme[slash:] if slash != -1 else "/"

    def find(self, locator):
        return self.wait.until(EC.presence_of_element_located(locator))

    def click(self, locator):
        self.wait.until(EC.element_to_be_clickable(locator)).click()

    def type(self, locator, text: str):
        element = self.find(locator)
        element.clear()
        element.send_keys(text)

    def wait_for_path(self, path: str):
        self.wait.until(lambda d: self.current_path.startswith(path))
        return self

    @property
    def body_text(self) -> str:
        return self.find(("tag name", "body")).text
