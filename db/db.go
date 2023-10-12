// db/db.go
package db

import (
	"gudang/models"
	"os"

	"github.com/jinzhu/gorm"
	_ "github.com/jinzhu/gorm/dialects/mysql"
)

var (
	DB *gorm.DB
)

func InitDB() (*gorm.DB, error) {
	var err error

	DB, err = gorm.Open("mysql", os.Getenv("DB_string"))
	if err != nil {
		return nil, err
	}

	DB.AutoMigrate(&models.Stock{})
	return DB, nil
}
