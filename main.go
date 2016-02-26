package main

import (
	"os"
	//"fmt"
	"log"

	"gopkg.in/BurntSushi/toml.v0"
	"gopkg.in/Netwurx/routeros-api-go.v0"
)

type tomlConfig struct {
	Routers map[string]router
}

type router struct {
	IP string
}

func ReadConfig() tomlConfig {
	var configfile = "config.toml"
	_, err := os.Stat(configfile)
	if err != nil {
		log.Fatal("Config file is missing: ", configfile)
	}

	var config tomlConfig
	if _, err := toml.DecodeFile(configfile, &config); err != nil {
		log.Fatal(err)
	}

	return config
}

func send(routerName string, command string) (routeros.Reply, error) {

	var err error

	ros, err := routeros.New("demo.mt.lv:8728")
	if err != nil {
		log.Fatal(err)
	}

	log.Print(ros)

	err = ros.Connect("admin", "")
	if err != nil {
		log.Fatal(err)
	}

	res, err := ros.Call(command, nil)

	log.Print(res)

	ros.Close()

	return res, err

}

func main() {

	config := ReadConfig()

	log.Print(config)

	var res, err = send("", "/system/resource/print")

	log.Print(res)

	if err != nil {
		log.Fatal(err)
	}

}
