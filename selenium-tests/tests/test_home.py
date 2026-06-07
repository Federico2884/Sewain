"""Tes beranda: halaman tampil dan kategori muncul."""

import pytest

from pages.home_page import HomePage


@pytest.mark.smoke
def test_beranda_menampilkan_hero(driver, base_url):
    home = HomePage(driver, base_url).open()

    assert "Sewa Barang" in home.hero_text()


@pytest.mark.smoke
def test_beranda_menampilkan_kategori(driver, base_url):
    home = HomePage(driver, base_url).open()

    # Kategori kurasi dari Item::CATEGORIES selalu tampil di beranda.
    assert home.has_category("Mobil")
    assert home.has_category("Kamera")


def test_pencarian_beranda_mengarah_ke_katalog(driver, base_url):
    home = HomePage(driver, base_url).open()
    home.search("Kamera")

    home.wait_for_path("/items")
    assert home.current_path.startswith("/items")
