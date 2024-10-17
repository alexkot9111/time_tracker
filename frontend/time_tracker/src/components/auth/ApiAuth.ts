import { instance } from "./ApiConfig.ts";

const AuthService = {
    login(email: string, password: string) {
        return instance.post("/api/login", { email, password });
    },

    refreshToken() {
        return instance.get("/api/token/refresh");
    },

    logout() {
        return instance.post("/api/logout");
    }
};

export default AuthService;