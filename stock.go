package main

import (
	"fmt"
	"html/template"
	"net/http"
	"time"

	"github.com/jinzhu/gorm"
	"github.com/labstack/echo/v4"
	"github.com/labstack/echo/v4/middleware"
)

// Model untuk data yang diambil dari Gorm
type Stock struct {
	ID         uint      `gorm:"primary_key" form:"id"`
	Tanggal    time.Time `form:"tanggal"`
	NamaBarang string    `form:"nama_barang"`
	Jumlah     int       `form:"jumlah"`
	Keterangan string    `form:"keterangan"`
	CreatedAt  time.Time
	UpdatedAt  time.Time
	DeletedAt  *time.Time `sql:"index"`
}

var db *gorm.DB
var err error

func main() {

	// Initialize Echo instance
	e := echo.New()

	// Middleware
	e.Use(middleware.Logger())
	e.Use(middleware.Recover())
	e.Use(middleware.CORS())

	// Database initialization
	if _, err := db.InitDB(); err != nil {
		e.Logger.Fatal(err)
	}

	// Routes
	e.GET("/", showStocks)

	// Start the server
	e.Start(":8080")
}

func showStocks(c echo.Context) error {
	var stocks []Stock
	db.Find(&stocks)

	return render(c, http.StatusOK, "index.html", map[string]interface{}{
		"title":  "Stock List",
		"stocks": stocks,
	})
}

func render(c echo.Context, code int, templateName string, data map[string]interface{}) error {
	tmpl, err := template.New(templateName).ParseFiles(templateName)
	if err != nil {
		return c.String(http.StatusInternalServerError,
			fmt.Sprintf("Internal server error: %v", err))
	}

	return tmpl.ExecuteTemplate(c.Response().Writer, "base", data)
}
