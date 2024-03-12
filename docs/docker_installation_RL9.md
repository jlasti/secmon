# How to install Docker on Rocky Linux 9

1. Add the official Docker repository to config-manager
```
sudo dnf config-manager --add-repo https://download.docker.com/linux/centos/docker-ce.repo
```
2. Install Docker, which is composed of three main packages
```
sudo dnf install docker-ce docker-ce-cli containerd.io
```

3. Start docker.service
```
sudo systemctl start docker
```

4. Verify if docker.service is running
```
sudo systemctl status docker
```

5. Enable docker.service to start at server reboot
```
sudo systemctl enable docker
```
----
