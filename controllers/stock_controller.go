// controllers/stock_controller.go
package controllers

import (
	"gudang/db"
	"gudang/models"
	"net/http"

	"github.com/jinzhu/gorm"
	"github.com/labstack/echo/v4"
)

func GetStocks(c echo.Context) error {
	var stocks []models.Stock

	if err := db.DB.Order("created_at desc").Find(&stocks).Error; err != nil {
		return c.JSON(http.StatusInternalServerError, map[string]interface{}{"error": err.Error()})
	}
	return c.JSON(http.StatusOK, stocks)
}

func GetStock(c echo.Context) error {
	id := c.Param("id")
	var stock models.Stock
	if err := db.DB.Where("id = ?", id).First(&stock).Error; err != nil {
		if gorm.IsRecordNotFoundError(err) {
			return c.JSON(http.StatusNotFound, map[string]interface{}{"error": "Stock not found"})
		}
		return c.JSON(http.StatusInternalServerError, map[string]interface{}{"error": err.Error()})
	}
	return c.JSON(http.StatusOK, stock)
}

func CreateStock(c echo.Context) error {
	var stock models.Stock
	if err := c.Bind(&stock); err != nil {
		return c.JSON(http.StatusBadRequest, map[string]interface{}{"error": err.Error()})
	}

	if err := db.DB.Create(&stock).Error; err != nil {
		return c.JSON(http.StatusInternalServerError, map[string]interface{}{"error": err.Error()})
	}

	return c.JSON(http.StatusCreated, stock)
}

func UpdateStock(c echo.Context) error {
	id := c.Param("id")
	var stock models.Stock
	if err := db.DB.Where("id = ?", id).First(&stock).Error; err != nil {
		if gorm.IsRecordNotFoundError(err) {
			return c.JSON(http.StatusNotFound, map[string]interface{}{"error": "Stock not found"})
		}
		return c.JSON(http.StatusInternalServerError, map[string]interface{}{"error": err.Error()})
	}

	if err := c.Bind(&stock); err != nil {
		return c.JSON(http.StatusBadRequest, map[string]interface{}{"error": err.Error()})
	}

	db.DB.Save(&stock)
	return c.JSON(http.StatusOK, stock)
}

func DeleteStock(c echo.Context) error {
	id := c.Param("id")
	var stock models.Stock
	if err := db.DB.Where("id = ?", id).First(&stock).Error; err != nil {
		if gorm.IsRecordNotFoundError(err) {
			return c.JSON(http.StatusNotFound, map[string]interface{}{"error": "Stock not found"})
		}
		return c.JSON(http.StatusInternalServerError, map[string]interface{}{"error": err.Error()})
	}

	if err := db.DB.Delete(&stock).Error; err != nil {
		return c.JSON(http.StatusInternalServerError, map[string]interface{}{"error": err.Error()})
	}

	return c.JSON(http.StatusNoContent, nil)
}

func showStocks(c echo.Context) error {
	return c.File("views/stocks.html")
}

// handlers.go
func ListStocks(c echo.Context) error {
	var stocks []models.Stock
	db.DB.Find(&stocks)
	return c.Render(http.StatusOK, "stocks/index.html", map[string]interface{}{
		"stocks": stocks,
	})
}
