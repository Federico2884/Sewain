"""Page Object untuk halaman katalog barang (/items)."""

from selenium.webdriver.common.by import By

from .base_page import BasePage


class CatalogPage(BasePage):
    PATH = "/items"

    SEARCH_INPUT = (By.NAME, "search")
    SEARCH_BUTTON = (By.XPATH, "//button[contains(., 'Cari')]")
    ITEM_CARDS = (By.CSS_SELECTOR, "a[href*='/items/']")

    def open(self):
        return self.visit(self.PATH)

    def open_category(self, name: str):
        self.click((By.XPATH, f"//a[normalize-space()='{name}' or contains(., '{name}')]"))
        return self

    def search(self, keyword: str):
        self.type(self.SEARCH_INPUT, keyword)
        self.click(self.SEARCH_BUTTON)
        return self

    def item_titles(self) -> list[str]:
        return [
            el.text
            for el in self.driver.find_elements(By.CSS_SELECTOR, "a[href*='/items/'] h3")
        ]

    def shows_item(self, name: str) -> bool:
        return any(name in title for title in self.item_titles())
