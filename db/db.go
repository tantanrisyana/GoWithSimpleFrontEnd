// db/db.go
package db

import (
	"fmt"
	"os"

	"gorm.io/driver/mysql"
	"gorm.io/gorm"
)

var DB *gorm.DB

func InitDB() {
	// Check if required environment variables are set
	requiredEnvVars := []string{"DB_USER", "DB_HOST", "DB_PORT", "DB_NAME"}
	for _, envVar := range requiredEnvVars {
		if os.Getenv(envVar) == "" {
			fmt.Printf("Error: Environment variable %s is not set\n", envVar)
			os.Exit(1)
		}
	}

	dsn := fmt.Sprintf("%v@tcp(%v:%v)/%v?charset=utf8mb4&parseTime=True&loc=Local",
		os.Getenv("DB_USER"),
		os.Getenv("DB_HOST"),
		os.Getenv("DB_PORT"),
		os.Getenv("DB_NAME"))

	var err error
	DB, err = gorm.Open(mysql.Open(dsn), &gorm.Config{})
	if err != nil {
		fmt.Printf("Error initializing database: %s\n", err)
		os.Exit(1)
	}
}
