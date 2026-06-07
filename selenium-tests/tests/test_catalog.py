"""Tes katalog: daftar barang & filter kategori."""

import pytest

from pages.catalog_page import CatalogPage


@pytest.mark.catalog
@pytest.mark.smoke
def test_katalog_menampilkan_barang(driver, base_url):
    catalog = CatalogPage(driver, base_url).open()

    assert len(catalog.item_titles()) > 0


@pytest.mark.catalog
def test_filter_kategori_kamera(driver, base_url):
    catalog = CatalogPage(driver, base_url).open()
    catalog.open_category("Kamera")

    catalog.wait_for_path("/items")
    # Item seed "Selenium Kamera Sony A7" ada di kategori Kamera.
    assert catalog.shows_item("Selenium Kamera Sony A7")


@pytest.mark.catalog
def test_pencarian_barang(driver, base_url):
    catalog = CatalogPage(driver, base_url).open()
    catalog.search("Drone")

    assert catalog.shows_item("Selenium Drone DJI Mini")
