// main.go
package main

import (
	"gudang/controllers"
	"gudang/db"
	"html/template"
	"io"

	"github.com/labstack/echo/v4"
	"github.com/labstack/echo/v4/middleware"
)

type TemplateRenderer struct {
	templates *template.Template
}

func (t *TemplateRenderer) Render(w io.Writer, name string, data interface{}, c echo.Context) error {
	return t.templates.ExecuteTemplate(w, name, data)
}

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

	// Renderer
	renderer := &TemplateRenderer{
		templates: template.Must(template.ParseGlob("views/*.html")),
	}
	e.Renderer = renderer

	// Routes
	e.GET("/stocks", controllers.GetStocks)
	e.GET("/stocks/:id", controllers.GetStock)
	e.POST("/stocks", controllers.CreateStock)
	e.PUT("/stocks/:id", controllers.UpdateStock)
	e.DELETE("/stocks/:id", controllers.DeleteStock)
	//e.GET("/", controllers.)
	e.GET("/stocks", controllers.GetStocks) // Tambahkan route untuk endpoint /stocks

	// Start server
	e.Logger.Fatal(e.Start(":8000"))
}
