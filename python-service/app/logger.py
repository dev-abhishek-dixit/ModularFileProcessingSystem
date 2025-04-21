def log(message):
    with open("log.txt", "a") as f:
        f.write(f"{message}\n")
