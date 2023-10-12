// models/stock.go
package models

import "time"

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
