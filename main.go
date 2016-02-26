package main

import (
"os"
"fmt"
"log"

"net/http"
"encoding/json"

"gopkg.in/BurntSushi/toml.v0"
"gopkg.in/Netwurx/routeros-api-go.v0"
"github.com/zenazn/goji"
"github.com/zenazn/goji/web"
valid "github.com/asaskevich/govalidator"
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
		log.Print(err)
		return routeros.Reply{}, err
	}

	log.Print(ros)

	err = ros.Connect("admin", "")
	if err != nil {
		log.Print(err)
		return routeros.Reply{}, err
	}

	res, err := ros.Call(command, nil)

	log.Print(res)

	ros.Close()

	return res, err

}

func jsonError(text string) string {
	message := map[string]string{"error": text}
	res, _ := json.Marshal(message)
	return string(res)
}

func handlePing(c web.C, w http.ResponseWriter, r *http.Request) {
	var param string = c.URLParams["host"]

	if (!valid.IsIP(param) && !valid.IsDNSName(param)) {
		fmt.Fprint(w, jsonError("This is not a hostname or an IP address"))
		return
	}
}


func handleTracert(c web.C, w http.ResponseWriter, r *http.Request) {
	var param string = c.URLParams["host"]

	if (!valid.IsIP(param) && !valid.IsDNSName(param)) {
		fmt.Fprint(w, jsonError("This is not a hostname or an IP address"))
		return
	}
}

func handleInfo(c web.C, w http.ResponseWriter, r *http.Request) {
	var res, err = send("", "/system/resource/print")

	if err != nil {
		log.Print(err)
		fmt.Fprint(w, jsonError(err.Error()))
	}

	log.Print(res)
}

func main() {

	config := ReadConfig()

	log.Print(config)

	goji.Get("/:router/ping/:host", handlePing)
	goji.Get("/:router/tracert/:host", handleTracert)
	goji.Get("/:router/info", handleInfo)

	goji.Get("/*", http.FileServer(http.Dir("web")))

	goji.Serve()

}
