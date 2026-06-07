"""Page Object untuk login Penyewa (User) dan Vendor."""

from selenium.webdriver.common.by import By

from .base_page import BasePage


class LoginPage(BasePage):
    """Login Penyewa (Breeze) di /login."""

    PATH = "/login"

    EMAIL = (By.NAME, "email")
    PASSWORD = (By.NAME, "password")
    SUBMIT = (By.XPATH, "//button[contains(., 'Masuk')]")
    ERROR = (By.CSS_SELECTOR, ".text-red-600, .text-red-700, [class*='text-red']")

    def open(self):
        return self.visit(self.PATH)

    def login(self, email: str, password: str):
        self.type(self.EMAIL, email)
        self.type(self.PASSWORD, password)
        self.click(self.SUBMIT)
        return self

    def has_error(self) -> bool:
        return len(self.driver.find_elements(*self.ERROR)) > 0


class VendorLoginPage(LoginPage):
    """Login Vendor (guard custom) di /vendor/login."""

    PATH = "/vendor/login"
