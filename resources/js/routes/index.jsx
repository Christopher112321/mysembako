import { useRoutes } from 'react-router-dom';
import AppRoutes from './AppRoutes';

const Page = () => {
    console.log('Page component rendered');
    const routes = useRoutes(AppRoutes);
    console.log('Routes result:', routes);
    return routes || <div style={{ padding: '20px' }}>Loading...</div>;
};

export default Page;