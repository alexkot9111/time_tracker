import { useEffect } from "react"
import { useSelector, useDispatch } from "react-redux";
import { RootState } from "./store/store";
import { BrowserRouter, Routes, Route } from 'react-router-dom'
import { observer } from "mobx-react-lite"
import 'bootstrap/dist/css/bootstrap.min.css'
import './App.css'
import PrivateRoute from "./components/auth/PrivateRoute"
import AuthStore from "./components/auth/Store"
import Navigation from './components/main/Navigation'
import LoginPage from './components/auth/Login'
import MainPage from './components/tracker/MainPage'
import UserList from './components/user/UserList'
import UserCreate from './components/user/UserCreate'
import UserEdit from './components/user/UserEdit'

const App = observer(() => {

    const dispatch = useDispatch();
    const authStore = AuthStore(dispatch);

    useEffect(() => {
        authStore.checkAuth();
    }, []);

    const isAuth = useSelector((state: RootState) => state.auth.isAuth);

      return (
          <BrowserRouter>
              <Navigation isAuth={isAuth} />
              <Routes>
                  {/*Accessible for all users*/}
                  <Route index element={<MainPage />} />
                  <Route path="/login" element={<LoginPage />} />

                  {/*Accessible for authorized users*/}
                  <Route path="/users" element={<PrivateRoute />}>
                      <Route path="/users" element={<UserList />} />
                      <Route path="/users/create" element={<UserCreate />} />
                      <Route path="/users/:userId/edit" element={<UserEdit />} />
                  </Route>
              </Routes>
          </BrowserRouter>
      )
})

export default App
