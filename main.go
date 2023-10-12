// main.go
package main

import (
	"fmt"
	"gudang/controllers"
	"gudang/db"
	"html/template"
	"io"
	"os"

	"github.com/joho/godotenv"
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
	loadEnv()
	db.InitDB()
	e := echo.New()

	// Middleware
	e.Use(middleware.LoggerWithConfig(middleware.LoggerConfig{
		Output: os.Stdout,
	}))
	e.Use(middleware.Recover())
	e.Use(middleware.CORS())

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

	// Start server
	e.Logger.Fatal(e.Start(":8000"))
}

func loadEnv() {
	err := godotenv.Load()
	if err != nil {
		fmt.Printf("Error loading .env file: %s\n", err)
		os.Exit(1)
	}
}
