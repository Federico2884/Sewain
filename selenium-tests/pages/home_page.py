"""Page Object untuk halaman beranda (welcome)."""

from selenium.webdriver.common.by import By

from .base_page import BasePage


class HomePage(BasePage):
    PATH = "/"

    HERO_HEADING = (By.TAG_NAME, "h1")
    SEARCH_INPUT = (By.NAME, "search")

    def open(self):
        return self.visit(self.PATH)

    def hero_text(self) -> str:
        return self.find(self.HERO_HEADING).text

    def has_category(self, name: str) -> bool:
        link = (By.XPATH, f"//a[contains(., '{name}')]")
        return len(self.driver.find_elements(*link)) > 0

    def search(self, keyword: str):
        self.type(self.SEARCH_INPUT, keyword)
        self.find(self.SEARCH_INPUT).submit()
        return self
